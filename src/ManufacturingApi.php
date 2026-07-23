<?php declare(strict_types=1);

namespace Mnf\NetteSdk;

use Mnf\NetteSdk\Endpoints\Manufacturing\ProductionLineEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\CreateProductionLine;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\UpdateProductionLine;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\ProductionLine;
use Mnf\NetteSdk\Endpoints\Shared\Requests\GridRequest;
use Mnf\NetteSdk\Endpoints\Shared\Responses\FiltersResponse;
use Mnf\NetteSdk\Endpoints\Shared\Responses\GridResponse;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\ServerException;

class ManufacturingApi
{
	private Client $client;

	private ProductionLineEndpoint $productionLineEndpoint;

	public function __construct(Client $client)
	{
		$this->client = $client;
		$this->productionLineEndpoint = new ProductionLineEndpoint($client);
	}

	/**
	 * @param list<string> $roles
	 */
	public function withIdentity(string|null $subject, array $roles = []): self
	{
		return new self($this->client->withIdentity($subject, $roles));
	}

	/**
	 * @return GridResponse<ProductionLine>
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLines(GridRequest $request): GridResponse
	{
		return $this->productionLineEndpoint->getProductionLines($request);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLineFilters(): FiltersResponse
	{
		return $this->productionLineEndpoint->getProductionLineFilters();
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function getProductionLine(string $id): ProductionLine
	{
		return $this->productionLineEndpoint->getProductionLine($id);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function createProductionLine(CreateProductionLine $request): ProductionLine
	{
		return $this->productionLineEndpoint->createProductionLine($request);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function updateProductionLine(string $id, UpdateProductionLine $request): ProductionLine
	{
		return $this->productionLineEndpoint->updateProductionLine($id, $request);
	}

	/**
	 * @throws ClientException
	 * @throws ServerException
	 */
	public function deleteProductionLine(string $id): void
	{
		$this->productionLineEndpoint->deleteProductionLine($id);
	}
}
