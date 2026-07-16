<?php declare(strict_types = 1);

namespace Satanio\SdkSkeleton;

use Satanio\SdkSkeleton\Endpoints\ExampleEndpoint;
use Satanio\SdkSkeleton\Endpoints\Requests\ExampleRequest;
use Satanio\SdkSkeleton\Endpoints\Responses\ExampleResponse;

class SdkSkeleton
{

	public function __construct(private readonly Client $client)
	{
	}

	public function example(ExampleRequest $request): ExampleResponse
	{
		return (new ExampleEndpoint($this->client))->example($request);
	}

}
