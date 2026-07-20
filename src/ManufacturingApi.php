<?php declare(strict_types=1);

namespace Mnf\NetteSdk;

use Mnf\NetteSdk\Endpoints\Manufacturing\ProductionLineEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLineItem;
use Mnf\NetteSdk\Endpoints\Shared\Requests\GridRequest;
use Mnf\NetteSdk\Endpoints\Shared\Responses\FiltersResponse;
use Mnf\NetteSdk\Endpoints\Shared\Responses\GridResponse;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\ServerException;

class ManufacturingApi
{
	private ProductionLineEndpoint $productionLineEndpoint;

	public function __construct(Client $client)
	{
		$this->productionLineEndpoint = new ProductionLineEndpoint($client);
	}

	/**
	 * @param list<string> $roles
	 * @return GridResponse<ProductionLineItem>
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLines(
		GridRequest $request,
		string|null $subject = null,
		array $roles = [],
	): GridResponse
	{
		return $this->productionLineEndpoint->getProductionLines($request, $subject, $roles);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLineFilters(): FiltersResponse
	{
		return $this->productionLineEndpoint->getProductionLineFilters();
	}
}
