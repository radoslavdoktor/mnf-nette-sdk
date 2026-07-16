<?php declare(strict_types = 1);

namespace Satanio\SdkSkeleton;

use Satanio\SdkSkeleton\Exceptions\ServerException;

class SdkSkeleton
{

	public function __construct(private readonly Client $client)
	{
	}

	public function example(string $input): string
	{
		$response = $this->client->sendRequest('POST', 'api/v1/example', [
			'json' => [
				'input' => $input,
			],
		]);

		$payload = $response['payload'] ?? null;

		if (!is_array($payload) || !array_key_exists('output', $payload) || !is_string($payload['output'])) {
			throw new ServerException('Payload error', 404);
		}

		return $payload['output'];
	}

}
