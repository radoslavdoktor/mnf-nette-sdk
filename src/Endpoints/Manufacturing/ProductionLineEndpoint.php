<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing;

use Mnf\NetteSdk\Endpoints\BaseEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\ProductionLineRequest;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineItem;
use Mnf\NetteSdk\Endpoints\ResponseList;
use Mnf\NetteSdk\Endpoints\Shared\Requests\GridRequest;
use Mnf\NetteSdk\Endpoints\Shared\Responses\FiltersResponse;
use Mnf\NetteSdk\Endpoints\Shared\Responses\GridResponse;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\ServerException;

class ProductionLineEndpoint extends BaseEndpoint
{
	/**
	 * @return GridResponse<ProductionLineItem>
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLines(GridRequest $request): GridResponse
	{
		return $this->getGridResponse('GET', 'api/v1/manufacturing/production-lines', $request->toArray(), ProductionLineItem::class);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLineFilters(): FiltersResponse
	{
		$response = $this->sendAuthenticatedRequest('GET', 'api/v1/manufacturing/production-lines/filters');

		return FiltersResponse::fromArray(ResponseList::assertArray($response->body));
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLine(string $id): ProductionLineItem
	{
		return $this->getItemResponse('GET', \sprintf('api/v1/manufacturing/production-lines/%s', $id), [], ProductionLineItem::class);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function createProductionLine(ProductionLineRequest $request): ProductionLineItem
	{
		return $this->getItemResponse('POST', 'api/v1/manufacturing/production-lines', ['json' => $request->toArray()], ProductionLineItem::class);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function updateProductionLine(string $id, ProductionLineRequest $request): ProductionLineItem
	{
		return $this->getItemResponse('PUT', \sprintf('api/v1/manufacturing/production-lines/%s', $id), ['json' => $request->toArray()], ProductionLineItem::class);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function deleteProductionLine(string $id): void
	{
		$this->sendAuthenticatedRequest('DELETE', \sprintf('api/v1/manufacturing/production-lines/%s', $id));
	}
}
