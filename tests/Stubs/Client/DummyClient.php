<?php declare(strict_types=1);

namespace Tests\Stubs\Client;

use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;

class DummyClient extends Client
{
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct()
	{
		parent::__construct('http://localhost', 'Syc1jJuyaafnDcDjxBA5nPWQyG/F4IF7brnDENdprRZmlq8bjDKCNZNJj4bTjzDAz4SXKn6niU7KaPIMj0UMcg==');
	}

	/**
	 * @param array<string, mixed> $options
	 * @return array<string, mixed>
	 */
	public function sendRequest(
		string $method,
		string $uri,
		array $options = [],
	): array
	{
		return ['output' => 'dummy-output'];
	}
}
