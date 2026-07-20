<?php declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Eddsa;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;
use Mnf\NetteSdk\Exceptions\ServerException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class ClientTest extends TestCase
{
	private const VALID_PRIVATE_KEY = 'Syc1jJuyaafnDcDjxBA5nPWQyG/F4IF7brnDENdprRZmlq8bjDKCNZNJj4bTjzDAz4SXKn6niU7KaPIMj0UMcg==';

	/**
	 * @throws InvalidArgumentException
	 */
	public function testConstructThrowsOnEmptyPrivateKey(): void
	{
		$this->expectException(InvalidArgumentException::class);

		new Client('http://localhost', '');
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testConstructThrowsOnInvalidBase64PrivateKey(): void
	{
		$this->expectException(InvalidArgumentException::class);

		new Client('http://localhost', 'not-valid-base64!!!');
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testConstructThrowsOnWrongLengthPrivateKey(): void
	{
		$this->expectException(InvalidArgumentException::class);

		new Client('http://localhost', \base64_encode('too-short'));
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testConstructSucceedsWithValidPrivateKey(): void
	{
		$this->expectNotToPerformAssertions();

		new Client('http://localhost', self::VALID_PRIVATE_KEY);
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestAttachesValidJwtAuthorizationHeader(): void
	{
		$capturedRequest = null;
		$client = $this->createClient([new GuzzleResponse(200, [], '{}')], $capturedRequest);

		$client->sendRequest('GET', '/anything');

		self::assertNotNull($capturedRequest);
		$authorization = $capturedRequest->getHeaderLine('Authorization');
		self::assertStringStartsWith('Bearer ', $authorization);

		$token = \substr($authorization, \strlen('Bearer '));
		self::assertNotSame('', $token);

		$secretKey = (string)\base64_decode(self::VALID_PRIVATE_KEY, true);
		self::assertNotSame('', $secretKey);
		$publicKey = \sodium_crypto_sign_publickey_from_secretkey($secretKey);

		$config = Configuration::forAsymmetricSigner(
			new Eddsa(),
			InMemory::base64Encoded(self::VALID_PRIVATE_KEY),
			InMemory::plainText($publicKey),
		);
		$parsedToken = $config->parser()->parse($token);

		self::assertTrue($config->validator()->validate(
			$parsedToken,
			new SignedWith($config->signer(), $config->verificationKey()),
		));
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestReturnsDecodedBodyAndHeaders(): void
	{
		$client = $this->createClient([
			new GuzzleResponse(200, ['X-Count' => '3'], self::jsonEncode(['id' => 'abc'])),
		]);

		$response = $client->sendRequest('GET', '/anything');

		self::assertSame(['id' => 'abc'], $response->body);
		self::assertSame('3', $response->getHeader('X-Count'));
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestThrowsClientExceptionWithParsedErrorOn4xx(): void
	{
		$client = $this->createClient([
			new GuzzleResponse(404, [], self::jsonEncode(['error' => [['message' => 'Not found', 'code' => 'NOT_FOUND']]])),
		]);

		try {
			$client->sendRequest('GET', '/missing');
			self::fail('Expected ClientException to be thrown.');
		} catch (ClientException $e) {
			self::assertSame('Not found', $e->getMessage());
			self::assertSame(404, $e->getCode());
			self::assertSame(['message' => 'Not found', 'code' => 'NOT_FOUND'], $e->getContext());
		}
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 */
	public function testSendRequestThrowsServerExceptionWithParsedErrorOn5xx(): void
	{
		$client = $this->createClient([
			new GuzzleResponse(500, [], self::jsonEncode(['error' => [['message' => 'Internal error']]])),
		]);

		try {
			$client->sendRequest('GET', '/broken');
			self::fail('Expected ServerException to be thrown.');
		} catch (ServerException $e) {
			self::assertSame('Internal error', $e->getMessage());
			self::assertSame(500, $e->getCode());
		}
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestThrowsClientExceptionOnMalformedJson(): void
	{
		$client = $this->createClient([
			new GuzzleResponse(200, [], '{not valid json'),
		]);

		$this->expectException(ClientException::class);

		$client->sendRequest('GET', '/anything');
	}

	/**
	 * @param array<array-key, mixed> $data
	 */
	private static function jsonEncode(array $data): string
	{
		$encoded = \json_encode($data);
		self::assertNotFalse($encoded);

		return $encoded;
	}

	/**
	 * @param list<GuzzleResponse> $responses
	 * @throws InvalidArgumentException
	 */
	private function createClient(array $responses, RequestInterface|null &$capturedRequest = null): Client
	{
		$handlerStack = HandlerStack::create(new MockHandler($responses));
		$handlerStack->push(static function (callable $handler) use (&$capturedRequest): callable {
			return static function (RequestInterface $request, array $options) use ($handler, &$capturedRequest) {
				$capturedRequest = $request;

				return $handler($request, $options);
			};
		});

		return new Client('http://localhost', self::VALID_PRIVATE_KEY, $handlerStack);
	}
}
