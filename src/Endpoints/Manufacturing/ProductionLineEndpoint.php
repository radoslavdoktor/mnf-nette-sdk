<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing;

use Mnf\NetteSdk\Endpoints\BaseEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\GetProductionLinesRequest;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineFiltersResponse;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineItem;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineListResponse;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\ServerException;

class ProductionLineEndpoint extends BaseEndpoint
{
	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLines(GetProductionLinesRequest $request): ProductionLineListResponse
	{
		$response = $this->client->sendRequest('GET', 'api/manufacturing/production-lines', [
			'query' => $request->toArray(),
		]);

		if (!\is_array($response->body)) {
			throw ServerException::payloadError();
		}

		$items = [];

		foreach ($response->body as $item) {
			if (!\is_array($item)) {
				throw ServerException::payloadError();
			}

			$items[] = ProductionLineItem::fromArray($item);
		}

		$totalCount = $response->getHeader('X-Count');

		return ProductionLineListResponse::create($items, $totalCount !== null ? (int)$totalCount : \count($items));
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLineFilters(): ProductionLineFiltersResponse
	{
		$response = $this->client->sendRequest('GET', 'api/manufacturing/production-lines/filters');

		if (!\is_array($response->body)) {
			throw ServerException::payloadError();
		}

		return ProductionLineFiltersResponse::fromArray($response->body);
	}
}
