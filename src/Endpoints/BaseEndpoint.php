<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints;

use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Endpoints\Shared\Responses\GridResponse;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\ServerException;

abstract class BaseEndpoint
{
	protected Client $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * @template TItem of IResponse
	 * @param array<string, mixed> $query
	 * @param class-string<TItem> $itemClass
	 * @param list<string> $roles
	 * @return GridResponse<TItem>
	 * @throws ClientException
	 * @throws ServerException
	 */
	protected function getGridResponse(
		string $method,
		string $uri,
		array $query,
		string $itemClass,
		string|null $subject = null,
		array $roles = [],
	): GridResponse
	{
		$response = $this->client->sendRequest($method, $uri, ['query' => $query], $subject, $roles);
		$items = ResponseList::parse($response->body, $itemClass);
		$totalCount = $response->getHeader('X-Count');

		return GridResponse::create($items, $totalCount !== null ? (int)$totalCount : \count($items));
	}
}
