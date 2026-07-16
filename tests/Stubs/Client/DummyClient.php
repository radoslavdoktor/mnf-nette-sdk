<?php declare(strict_types = 1);

namespace Tests\Stubs\Client;

use Satanio\SdkSkeleton\Client;

class DummyClient extends Client
{

	public function __construct()
	{
		parent::__construct('http://localhost', 'dummy-signing-key');
	}

	/**
	 * @param mixed[] $options
	 * @return mixed[]
	 */
	public function sendRequest(string $method, string $uri, array $options = []): array
	{
		return ['payload' => ['output' => 'dummy-output']];
	}

}
