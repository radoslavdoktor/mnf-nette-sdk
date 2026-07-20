<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing;

use Mnf\NetteSdk\Endpoints\BaseEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineItem;
use Mnf\NetteSdk\Endpoints\Responses\ResponseList;
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
		return $this->getGridResponse('GET', 'api/manufacturing/production-lines', $request->toArray(), ProductionLineItem::class);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLineFilters(): FiltersResponse
	{
		$response = $this->client->sendRequest('GET', 'api/manufacturing/production-lines/filters');

		return FiltersResponse::fromArray(ResponseList::assertArray($response->body));
	}
}
