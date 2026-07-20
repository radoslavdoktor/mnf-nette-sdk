<?php declare(strict_types=1);

namespace Mnf\NetteSdk;

use Mnf\NetteSdk\Endpoints\Manufacturing\ProductionLineEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\GetProductionLinesRequest;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineFiltersResponse;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineListResponse;
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
	public function getProductionLines(GetProductionLinesRequest $request): ProductionLineListResponse
	{
		return (new ProductionLineEndpoint($this->client))->getProductionLines($request);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLineFilters(): ProductionLineFiltersResponse
	{
		return (new ProductionLineEndpoint($this->client))->getProductionLineFilters();
	}
}
