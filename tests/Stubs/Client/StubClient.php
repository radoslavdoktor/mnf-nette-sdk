<?php declare(strict_types=1);

namespace Tests\Stubs\Client;

use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;
use Mnf\NetteSdk\Http\Response;

class StubClient extends Client
{
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(private readonly Response $response)
	{
		parent::__construct('http://localhost', 'Syc1jJuyaafnDcDjxBA5nPWQyG/F4IF7brnDENdprRZmlq8bjDKCNZNJj4bTjzDAz4SXKn6niU7KaPIMj0UMcg==');
	}

	/**
	 * @param array<string, mixed> $options
	 * @param list<string> $roles
	 */
	public function sendRequest(
		string $method,
		string $uri,
		array $options = [],
		string|null $subject = null,
		array $roles = [],
	): Response
	{
		return $this->response;
	}
}
