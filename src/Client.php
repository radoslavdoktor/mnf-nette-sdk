<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton;

use DateTimeImmutable;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use JsonException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;
use Satanio\SdkSkeleton\Exceptions\ClientException;
use Satanio\SdkSkeleton\Exceptions\InvalidArgumentException;
use Satanio\SdkSkeleton\Exceptions\ServerException;

class Client
{
	private HttpClient $httpClient;

	/** @var non-empty-string */
	private string $signingKey;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $endpoint, string $signingKey)
	{
		if ($signingKey === '') {
			throw InvalidArgumentException::emptySigningKey();
		}

		$this->httpClient = new HttpClient(['base_uri' => $endpoint]);
		$this->signingKey = $signingKey;
	}

	/**
	 * @param array<string, mixed> $options
	 * @return array<string, mixed>
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function sendRequest(
		string $method,
		string $uri,
		array $options = [],
	): array
	{
		/** @var array<string, string> $headers */
		$headers = \is_array($options['headers'] ?? null) ? $options['headers'] : [];
		$headers['Authorization'] = \sprintf('Bearer %s', $this->createAccessToken());
		$options['headers'] = $headers;

		try {
			$response = $this->httpClient->request($method, $uri, $options);

			try {
				/** @var array<string, mixed> $decoded */
				$decoded = \json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

				return $decoded;
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
			new Sha512(),
			InMemory::plainText($this->signingKey),
			fn (Builder $builder) => $builder->issuedAt(new DateTimeImmutable()),
		);

		return $token->toString();
	}
}
