<?php declare(strict_types=1);

namespace Tests;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Eddsa;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;
use Mnf\NetteSdk\Exceptions\ServerException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
		$capturedOptions = null;
		$client = $this->createClient(
			static function (string $method, string $url, array $options) use (&$capturedOptions): MockResponse {
				$capturedOptions = $options;

				return new MockResponse('{}', ['http_code' => 200]);
			},
		);

		$client->sendRequest('GET', '/anything', $client->createAuthorizationHeader());

		self::assertIsArray($capturedOptions);
		$authorization = $this->findHeader($capturedOptions, 'Authorization');
		self::assertNotNull($authorization);
		self::assertStringStartsWith('Bearer ', $authorization);

		$token = \substr($authorization, \strlen('Bearer '));
		self::assertNotSame('', $token);

		$this->parseToken($token);
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestSignsSubjectAndRolesClaims(): void
	{
		$capturedOptions = null;
		$client = $this->createClient(
			static function (string $method, string $url, array $options) use (&$capturedOptions): MockResponse {
				$capturedOptions = $options;

				return new MockResponse('{}', ['http_code' => 200]);
			},
		);

		$client = $client->withIdentity('admin-42', ['role-a', 'role-b']);
		$client->sendRequest('GET', '/anything', $client->createAuthorizationHeader());

		self::assertIsArray($capturedOptions);
		$authorization = $this->findHeader($capturedOptions, 'Authorization');
		self::assertNotNull($authorization);

		$token = \substr($authorization, \strlen('Bearer '));
		$parsedToken = $this->parseToken($token);

		self::assertSame('admin-42', $parsedToken->claims()->get('sub'));
		self::assertSame(['role-a', 'role-b'], $parsedToken->claims()->get('roles'));
		self::assertNotNull($parsedToken->claims()->get('exp'));
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testWithIdentityDoesNotMutateOriginalClient(): void
	{
		$client = $this->createClient(new MockResponse('{}', ['http_code' => 200]));

		$scopedClient = $client->withIdentity('admin-42', ['role-a']);

		$parsedToken = $this->parseToken(\substr($client->createAuthorizationHeader(), \strlen('Bearer ')));
		self::assertFalse($parsedToken->claims()->has('sub'));

		$parsedScopedToken = $this->parseToken(\substr($scopedClient->createAuthorizationHeader(), \strlen('Bearer ')));
		self::assertSame('admin-42', $parsedScopedToken->claims()->get('sub'));
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestOmitsSubjectAndRolesClaimsWhenNotGiven(): void
	{
		$capturedOptions = null;
		$client = $this->createClient(
			static function (string $method, string $url, array $options) use (&$capturedOptions): MockResponse {
				$capturedOptions = $options;

				return new MockResponse('{}', ['http_code' => 200]);
			},
		);

		$client->sendRequest('GET', '/anything', $client->createAuthorizationHeader());

		self::assertIsArray($capturedOptions);
		$authorization = $this->findHeader($capturedOptions, 'Authorization');
		self::assertNotNull($authorization);

		$token = \substr($authorization, \strlen('Bearer '));
		$parsedToken = $this->parseToken($token);

		self::assertFalse($parsedToken->claims()->has('sub'));
		self::assertFalse($parsedToken->claims()->has('roles'));
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestReturnsDecodedBodyAndHeaders(): void
	{
		$client = $this->createClient(
			new MockResponse(self::jsonEncode(['id' => 'abc']), [
				'http_code' => 200,
				'response_headers' => ['X-Count' => '3'],
			]),
		);

		$response = $client->sendRequest('GET', '/anything', $client->createAuthorizationHeader());

		self::assertSame(['id' => 'abc'], $response->body);
		self::assertSame('3', $response->getHeader('X-Count'));
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestReturnsNullBodyOnEmptyResponse(): void
	{
		$client = $this->createClient(new MockResponse('', ['http_code' => 204]));

		$response = $client->sendRequest('DELETE', '/anything', $client->createAuthorizationHeader());

		self::assertNull($response->body);
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestThrowsClientExceptionWithParsedErrorOn4xx(): void
	{
		$client = $this->createClient(
			new MockResponse(
				self::jsonEncode(['error' => [['message' => 'Not found', 'code' => 'NOT_FOUND']]]),
				['http_code' => 404],
			),
		);

		try {
			$client->sendRequest('GET', '/missing', $client->createAuthorizationHeader());
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
		$client = $this->createClient(
			new MockResponse(
				self::jsonEncode(['error' => [['message' => 'Internal error']]]),
				['http_code' => 500],
			),
		);

		try {
			$client->sendRequest('GET', '/broken', $client->createAuthorizationHeader());
			self::fail('Expected ServerException to be thrown.');
		} catch (ServerException $e) {
			self::assertSame('Internal error', $e->getMessage());
			self::assertSame(500, $e->getCode());
		}
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 */
	public function testSendRequestThrowsServerExceptionOnTransportFailure(): void
	{
		$client = $this->createClient(new MockResponse('', ['error' => 'Connection refused']));

		try {
			$client->sendRequest('GET', '/unreachable', $client->createAuthorizationHeader());
			self::fail('Expected ServerException to be thrown.');
		} catch (ServerException $e) {
			self::assertSame('Connection refused', $e->getMessage());
			self::assertInstanceOf(TransportExceptionInterface::class, $e->getPrevious());
		}
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testSendRequestThrowsClientExceptionOnMalformedJson(): void
	{
		$client = $this->createClient(new MockResponse('{not valid json', ['http_code' => 200]));

		$this->expectException(ClientException::class);

		$client->sendRequest('GET', '/anything', $client->createAuthorizationHeader());
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
	 * @param array<array-key, mixed> $options
	 */
	private function findHeader(array $options, string $name): string|null
	{
		$normalizedHeaders = \is_array($options['normalized_headers'] ?? null) ? $options['normalized_headers'] : [];
		$headers = \is_array($normalizedHeaders[\strtolower($name)] ?? null) ? $normalizedHeaders[\strtolower($name)] : [];

		foreach ($headers as $header) {
			if (\is_string($header) && \stripos($header, $name . ':') === 0) {
				return \trim(\substr($header, \strlen($name) + 1));
			}
		}

		return null;
	}

	private function parseToken(string $token): Plain
	{
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

		self::assertInstanceOf(Plain::class, $parsedToken);
		self::assertTrue($config->validator()->validate(
			$parsedToken,
			new SignedWith($config->signer(), $config->verificationKey()),
		));

		return $parsedToken;
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function createClient(callable|MockResponse $responseFactory): Client
	{
		$httpClient = new MockHttpClient($responseFactory, 'http://localhost');

		return new Client('http://localhost', self::VALID_PRIVATE_KEY, $httpClient);
	}
}
