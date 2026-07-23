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

	private string|null $subject = null;

	/** @var list<string> */
	private array $roles = [];

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
	 * Returns a clone of this client that authenticates as the given subject/roles.
	 *
	 * @param list<string> $roles
	 */
	public function withIdentity(string|null $subject, array $roles = []): static
	{
		$clone = clone $this;
		$clone->subject = $subject;
		$clone->roles = $roles;

		return $clone;
	}

	public function createAuthorizationHeader(): string
	{
		return \sprintf('Bearer %s', $this->createAccessToken());
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

		$content = $response->getContent();

		if ($content === '') {
			return new Response(null, $response->getHeaders());
		}

		try {
			$decoded = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

			return new Response($decoded, $response->getHeaders());
		} catch (JsonException $e) {
			throw ClientException::invalidJsonResponse($e);
		}
	}

	private function createAccessToken(): string
	{
		$facade = new JwtFacade();
		$now = new DateTimeImmutable();

		$token = $facade->issue(
			new Eddsa(),
			$this->privateKey,
			function (Builder $builder) use ($now): Builder {
				$builder = $builder
					->issuedAt($now)
					->expiresAt($now->modify(\sprintf('+%d seconds', self::ACCESS_TOKEN_TTL_SECONDS)));

				if ($this->subject !== null && $this->subject !== '') {
					$builder = $builder->relatedTo($this->subject);
				}

				if ($this->roles !== []) {
					$builder = $builder->withClaim('roles', $this->roles);
				}

				return $builder;
			},
		);

		return $token->toString();
	}
}
