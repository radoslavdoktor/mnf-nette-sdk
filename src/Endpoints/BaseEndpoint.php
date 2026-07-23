<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints;

use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Endpoints\Shared\Responses\GridResponse;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\ServerException;
use Mnf\NetteSdk\Http\Response;

abstract class BaseEndpoint
{
	protected Client $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * @param array<string, mixed> $options
	 * @throws ClientException
	 * @throws ServerException
	 */
	protected function sendAuthenticatedRequest(
		string $method,
		string $uri,
		array $options = [],
	): Response
	{
		$authorization = $this->client->createAuthorizationHeader();

		return $this->client->sendRequest($method, $uri, $authorization, $options);
	}

	/**
	 * @template TItem of IResponse
	 * @param array<string, mixed> $query
	 * @param class-string<TItem> $itemClass
	 * @return GridResponse<TItem>
	 * @throws ClientException
	 * @throws ServerException
	 */
	protected function getGridResponse(
		string $method,
		string $uri,
		array $query,
		string $itemClass,
	): GridResponse
	{
		$response = $this->sendAuthenticatedRequest($method, $uri, ['query' => $query]);
		$items = ResponseList::parse($response->body, $itemClass);
		$totalCount = $response->getHeader('X-Count');

		return GridResponse::create($items, $totalCount !== null ? (int)$totalCount : \count($items));
	}

	/**
	 * @template TItem of IResponse
	 * @param array<string, mixed> $options
	 * @param class-string<TItem> $itemClass
	 * @return TItem
	 * @throws ClientException
	 * @throws ServerException
	 */
	protected function getItemResponse(
		string $method,
		string $uri,
		array $options,
		string $itemClass,
	): IResponse
	{
		$response = $this->sendAuthenticatedRequest($method, $uri, $options);

		/** @var TItem $item */
		$item = $itemClass::fromArray(ResponseList::assertArray($response->body));

		return $item;
	}
}
