<?php declare(strict_types=1);

namespace Tests\Stubs\Client;

use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;
use Mnf\NetteSdk\Http\Response;

class StubClient extends Client
{
	public string|null $capturedMethod = null;

	public string|null $capturedUri = null;

	/** @var array<string, mixed>|null */
	public array|null $capturedOptions = null;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(private readonly Response $response)
	{
		parent::__construct('http://localhost', 'Syc1jJuyaafnDcDjxBA5nPWQyG/F4IF7brnDENdprRZmlq8bjDKCNZNJj4bTjzDAz4SXKn6niU7KaPIMj0UMcg==');
	}

	/**
	 * @param array<string, mixed> $options
	 */
	public function sendRequest(
		string $method,
		string $uri,
		string $authorizationHeader,
		array $options = [],
	): Response
	{
		$this->capturedMethod = $method;
		$this->capturedUri = $uri;
		$this->capturedOptions = $options;

		return $this->response;
	}
}
