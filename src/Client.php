<?php declare(strict_types=1);

namespace Mnf\NetteSdk;

use DateTimeImmutable;
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
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClientFactory;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
	private const int ACCESS_TOKEN_TTL_SECONDS = 60;

	private HttpClientInterface $httpClient;

	private Key $privateKey;

	/**
	 * @param string $privateKey base64-encoded Ed25519 private key
	 * @param HttpClientInterface|null $httpClient overrides the HTTP client; for tests only
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		string $baseUri,
		string $privateKey,
		HttpClientInterface|null $httpClient = null,
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

		$this->httpClient = ($httpClient ?? SymfonyHttpClientFactory::create())->withOptions(['base_uri' => $baseUri]);
		$this->privateKey = $key;
	}

	/**
	 * @param list<string> $roles
	 */
	public function createAuthorizationHeader(string|null $subject = null, array $roles = []): string
	{
		return \sprintf('Bearer %s', $this->createAccessToken($subject, $roles));
	}

	/**
	 * @param array<string, mixed> $options
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function sendRequest(
		string $method,
		string $uri,
		string $authorizationHeader,
		array $options = [],
	): Response
	{
		/** @var array<string, string> $headers */
		$headers = \is_array($options['headers'] ?? null) ? $options['headers'] : [];
		$headers['Authorization'] = $authorizationHeader;
		$options['headers'] = $headers;

		try {
			$response = $this->httpClient->request($method, $uri, $options);
			$statusCode = $response->getStatusCode();
		} catch (TransportExceptionInterface $e) {
			throw ServerException::createFromTransportException($e);
		}

		if ($statusCode >= 500) {
			throw ServerException::createFromResponse($response);
		}

		if ($statusCode >= 400) {
			throw ClientException::createFromResponse($response);
		}

		try {
			$decoded = \json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

			return new Response($decoded, $response->getHeaders());
		} catch (JsonException $e) {
			throw ClientException::invalidJsonResponse($e);
		}
	}

	/**
	 * @param list<string> $roles
	 */
	private function createAccessToken(string|null $subject, array $roles): string
	{
		$facade = new JwtFacade();
		$now = new DateTimeImmutable();

		$token = $facade->issue(
			new Eddsa(),
			$this->privateKey,
			function (Builder $builder) use ($now, $subject, $roles): Builder {
				$builder = $builder
					->issuedAt($now)
					->expiresAt($now->modify(\sprintf('+%d seconds', self::ACCESS_TOKEN_TTL_SECONDS)));

				if ($subject !== null && $subject !== '') {
					$builder = $builder->relatedTo($subject);
				}

				if ($roles !== []) {
					$builder = $builder->withClaim('roles', $roles);
				}

				return $builder;
			},
		);

		return $token->toString();
	}
}
