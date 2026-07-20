<?php declare(strict_types=1);

namespace Tests\Endpoints\Manufacturing;

use Mnf\NetteSdk\Endpoints\Manufacturing\ProductionLineEndpoint;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\FilterOperator;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\GetProductionLinesRequest;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\ProductionLineFilter;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\ProductionLineSort;
use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\SortDirection;
use Mnf\NetteSdk\Endpoints\Manufacturing\Responses\FilterType;
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
			['id' => 'a', 'name' => 'Line A', 'description' => null, 'active' => true, 'inputPositionId' => null, 'outputPositionId' => null],
			['id' => 'b', 'name' => 'Line B', 'description' => 'desc', 'active' => false, 'inputPositionId' => 'in', 'outputPositionId' => 'out'],
		];
		$client = new StubClient(new Response($body, ['X-Count' => ['42']]));
		$endpoint = new ProductionLineEndpoint($client);

		$response = $endpoint->getProductionLines(GetProductionLinesRequest::create());

		self::assertSame(42, $response->getTotalCount());
		self::assertCount(2, $response->getItems());
		self::assertSame('a', $response->getItems()[0]->getId());
		self::assertSame('Line A', $response->getItems()[0]->getName());
		self::assertTrue($response->getItems()[0]->isActive());
		self::assertSame('out', $response->getItems()[1]->getOutputPositionId());
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

		$response = $endpoint->getProductionLines(GetProductionLinesRequest::create());

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
	}

	public function testGetProductionLinesRequestBuildsQueryWithSortAndFilter(): void
	{
		$request = GetProductionLinesRequest::create(
			offset: 10,
			limit: 5,
			sorts: [ProductionLineSort::create('name', SortDirection::Ascending)],
			filters: [ProductionLineFilter::create('active', FilterOperator::Equal, '1')],
		);

		self::assertSame([
			'offset' => 10,
			'limit' => 5,
			'sort' => ['name' => 'asc'],
			'filter' => ['active' => ['eq' => '1']],
		], $request->toArray());
	}
}
