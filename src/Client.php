<?php declare(strict_types=1);

namespace Mnf\NetteSdk;

use DateTimeImmutable;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use GuzzleHttp\HandlerStack;
use JsonException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Exception as JwtException;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Eddsa;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;
use Mnf\NetteSdk\Exceptions\ServerException;
use Mnf\NetteSdk\Http\Response;

class Client
{
	private HttpClient $httpClient;

	private Key $privateKey;

	/**
	 * @param string $privateKey base64-encoded Ed25519 private key
	 * @param HandlerStack|null $handlerStack overrides Guzzle's HTTP handler; for tests only
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		string $baseUri,
		string $privateKey,
		HandlerStack|null $handlerStack = null,
	)
	{
		if ($privateKey === '') {
			throw InvalidArgumentException::emptyPrivateKey();
		}

		try {
			$key = InMemory::base64Encoded($privateKey);
		} catch (JwtException $e) { // @phpstan-ignore catch.neverThrown (undocumented throw on invalid base64 content)
			throw InvalidArgumentException::invalidPrivateKey($e);
		}

		if (\mb_strlen($key->contents(), '8bit') !== \SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
			throw InvalidArgumentException::invalidPrivateKey();
		}

		$options = ['base_uri' => $baseUri];

		if ($handlerStack !== null) {
			$options['handler'] = $handlerStack;
		}

		$this->httpClient = new HttpClient($options);
		$this->privateKey = $key;
	}

	/**
	 * @param array<string, mixed> $options
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function sendRequest(
		string $method,
		string $uri,
		array $options = [],
	): Response
	{
		/** @var array<string, string> $headers */
		$headers = \is_array($options['headers'] ?? null) ? $options['headers'] : [];
		$headers['Authorization'] = \sprintf('Bearer %s', $this->createAccessToken());
		$options['headers'] = $headers;

		try {
			$response = $this->httpClient->request($method, $uri, $options);

			try {
				$decoded = \json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

				/** @var array<string, array<int, string>> $responseHeaders */
				$responseHeaders = $response->getHeaders();

				return new Response($decoded, $responseHeaders);
			} catch (JsonException $e) {
				throw ClientException::invalidJsonResponse($e);
			}
		} catch (GuzzleClientException $e) {
			throw ClientException::createFromBadResponseException($e);
		} catch (GuzzleServerException $e) {
			throw ServerException::createFromBadResponseException($e);
		}
	}

	private function createAccessToken(): string
	{
		$facade = new JwtFacade();

		$token = $facade->issue(
			new Eddsa(),
			$this->privateKey,
			fn (Builder $builder) => $builder->issuedAt(new DateTimeImmutable()),
		);

		return $token->toString();
	}
}
