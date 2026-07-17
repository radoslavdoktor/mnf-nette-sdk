<?php declare(strict_types=1);

namespace Tests\Stubs\Client;

use Satanio\SdkSkeleton\Client;
use Satanio\SdkSkeleton\Exceptions\InvalidArgumentException;

class DummyClient extends Client
{
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct()
	{
		parent::__construct('http://localhost', 'dummy-signing-key');
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
		return ['payload' => ['output' => 'dummy-output']];
	}
}
