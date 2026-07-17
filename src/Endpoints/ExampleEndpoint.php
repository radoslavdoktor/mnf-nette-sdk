<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton\Endpoints;

use Satanio\SdkSkeleton\Endpoints\Requests\ExampleRequest;
use Satanio\SdkSkeleton\Endpoints\Responses\ExampleResponse;
use Satanio\SdkSkeleton\Exceptions\ClientException;
use Satanio\SdkSkeleton\Exceptions\ServerException;

class ExampleEndpoint extends BaseEndpoint
{
	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function example(ExampleRequest $request): ExampleResponse
	{
		$response = $this->client->sendRequest('POST', 'api/v1/example', [
			'json' => $request->toArray(),
		]);

		$payload = $response['payload'] ?? null;

		if (!\is_array($payload)) {
			throw ServerException::payloadError();
		}

		return ExampleResponse::fromArray($payload);
	}
}
