<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton\Endpoints;

use Satanio\SdkSkeleton\Client;

abstract class BaseEndpoint
{
	protected Client $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}
}
