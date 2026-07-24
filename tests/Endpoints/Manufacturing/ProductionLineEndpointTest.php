<?php declare(strict_types=1);

namespace Tests\Endpoints\Manufacturing;

use Mnf\NetteSdk\Endpoints\Manufacturing\ProductionLineEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\CreateProductionLine;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\UpdateProductionLine;
use Mnf\NetteSdk\Endpoints\Shared\Requests\GridRequest;
use Mnf\NetteSdk\Endpoints\Shared\Responses\FilterType;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;
use Mnf\NetteSdk\Exceptions\ServerException;
use Mnf\NetteSdk\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Client\StubClient;

class ProductionLineEndpointTest extends TestCase
{
	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testGetProductionLinesUsesCountHeader(): void
	{
		$body = [
			['id' => 'a', 'name' => 'Line A', 'description' => null, 'active' => true, 'inputPositionId' => null, 'outputPositionId' => null, 'warehouseId' => null],
			['id' => 'b', 'name' => 'Line B', 'description' => 'desc', 'active' => false, 'inputPositionId' => 'in', 'outputPositionId' => 'out', 'warehouseId' => 'wh-1'],
		];
		$client = new StubClient(new Response($body, ['X-Count' => ['42']]));
		$endpoint = new ProductionLineEndpoint($client);

		$response = $endpoint->getProductionLines(GridRequest::create());

		self::assertSame(42, $response->getTotalCount());
		self::assertCount(2, $response->getItems());
		self::assertSame('a', $response->getItems()[0]->getId());
		self::assertSame('Line A', $response->getItems()[0]->getName());
		self::assertTrue($response->getItems()[0]->isActive());
		self::assertSame('out', $response->getItems()[1]->getOutputPositionId());
		self::assertSame('wh-1', $response->getItems()[1]->getWarehouseId());
		self::assertSame('GET', $client->capturedMethod);
		self::assertSame('api/v1/manufacturing/production-lines', $client->capturedUri);
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testGetProductionLinesFallsBackToItemCountWithoutHeader(): void
	{
		$client = new StubClient(new Response([], []));
		$endpoint = new ProductionLineEndpoint($client);

		$response = $endpoint->getProductionLines(GridRequest::create());

		self::assertSame(0, $response->getTotalCount());
		self::assertSame([], $response->getItems());
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testGetProductionLineFilters(): void
	{
		$body = [
			'filters' => [
				[
					'attribute' => 'active',
					'type' => 'select',
					'options' => [
						['name' => 'Active', 'value' => '1'],
						['name' => 'Inactive', 'value' => '0'],
					],
				],
			],
		];
		$client = new StubClient(new Response($body, []));
		$endpoint = new ProductionLineEndpoint($client);

		$response = $endpoint->getProductionLineFilters();

		self::assertCount(1, $response->getFilters());
		self::assertSame('active', $response->getFilters()[0]->getAttribute());
		self::assertSame(FilterType::Select, $response->getFilters()[0]->getType());
		self::assertCount(2, $response->getFilters()[0]->getOptions());
		self::assertSame('Active', $response->getFilters()[0]->getOptions()[0]->getName());
		self::assertSame('GET', $client->capturedMethod);
		self::assertSame('api/v1/manufacturing/production-lines/filters', $client->capturedUri);
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testGetProductionLine(): void
	{
		$body = ['id' => 'a', 'name' => 'Line A', 'description' => null, 'active' => true, 'inputPositionId' => null, 'outputPositionId' => null, 'warehouseId' => 'wh-1'];
		$client = new StubClient(new Response($body, []));
		$endpoint = new ProductionLineEndpoint($client);

		$item = $endpoint->getProductionLine('a');

		self::assertSame('a', $item->getId());
		self::assertSame('Line A', $item->getName());
		self::assertSame('wh-1', $item->getWarehouseId());
		self::assertSame('GET', $client->capturedMethod);
		self::assertSame('api/v1/manufacturing/production-lines/a', $client->capturedUri);
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testCreateProductionLine(): void
	{
		$body = ['id' => 'a', 'name' => 'Line A', 'description' => 'desc', 'active' => true, 'inputPositionId' => 'in', 'outputPositionId' => 'out', 'warehouseId' => 'wh-1'];
		$client = new StubClient(new Response($body, []));
		$endpoint = new ProductionLineEndpoint($client);

		$item = $endpoint->createProductionLine(CreateProductionLine::create('a', 'Line A', 'desc', true, 'in', 'out', 'wh-1'));

		self::assertSame('a', $item->getId());
		self::assertSame('Line A', $item->getName());
		self::assertSame('in', $item->getInputPositionId());
		self::assertSame('out', $item->getOutputPositionId());
		self::assertSame('wh-1', $item->getWarehouseId());
		self::assertSame('POST', $client->capturedMethod);
		self::assertSame('api/v1/manufacturing/production-lines', $client->capturedUri);
		self::assertSame(
			['id' => 'a', 'name' => 'Line A', 'description' => 'desc', 'active' => true, 'inputPositionId' => 'in', 'outputPositionId' => 'out', 'warehouseId' => 'wh-1'],
			$client->capturedOptions['json'] ?? null,
		);
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testUpdateProductionLine(): void
	{
		$body = ['id' => 'a', 'name' => 'Line A updated', 'description' => null, 'active' => false, 'inputPositionId' => null, 'outputPositionId' => null, 'warehouseId' => 'wh-1'];
		$client = new StubClient(new Response($body, []));
		$endpoint = new ProductionLineEndpoint($client);

		$item = $endpoint->updateProductionLine('a', UpdateProductionLine::create('Line A updated', active: false, warehouseId: 'wh-1'));

		self::assertSame('Line A updated', $item->getName());
		self::assertFalse($item->isActive());
		self::assertSame('wh-1', $item->getWarehouseId());
		self::assertSame('PUT', $client->capturedMethod);
		self::assertSame('api/v1/manufacturing/production-lines/a', $client->capturedUri);
		self::assertIsArray($client->capturedOptions['json'] ?? null);
		self::assertArrayNotHasKey('id', $client->capturedOptions['json']);
		self::assertSame('wh-1', $client->capturedOptions['json']['warehouseId'] ?? null);
	}

	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testDeleteProductionLine(): void
	{
		$client = new StubClient(new Response(null, []));
		$endpoint = new ProductionLineEndpoint($client);

		$endpoint->deleteProductionLine('a');

		self::assertSame('DELETE', $client->capturedMethod);
		self::assertSame('api/v1/manufacturing/production-lines/a', $client->capturedUri);
	}
}
