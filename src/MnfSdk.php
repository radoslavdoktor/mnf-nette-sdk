<?php declare(strict_types=1);

namespace Mnf\NetteSdk;

use Mnf\NetteSdk\Endpoints\ExampleEndpoint;
use Mnf\NetteSdk\Endpoints\Requests\ExampleRequest;
use Mnf\NetteSdk\Endpoints\Responses\ExampleResponse;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\ServerException;

class MnfSdk
{
	public function __construct(private readonly Client $client)
	{
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function example(ExampleRequest $request): ExampleResponse
	{
		return (new ExampleEndpoint($this->client))->example($request);
	}
}
