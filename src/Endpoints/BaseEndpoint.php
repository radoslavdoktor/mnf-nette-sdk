<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints;

use Mnf\NetteSdk\Client;

abstract class BaseEndpoint
{
	protected Client $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}
}
