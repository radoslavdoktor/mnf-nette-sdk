<?php declare(strict_types=1);

namespace Tests\Endpoints\Shared\Requests;

use Mnf\NetteSdk\Endpoints\Shared\Requests\Filter;
use Mnf\NetteSdk\Endpoints\Shared\Requests\FilterOperator;
use Mnf\NetteSdk\Endpoints\Shared\Requests\GridRequest;
use Mnf\NetteSdk\Endpoints\Shared\Requests\Sort;
use Mnf\NetteSdk\Endpoints\Shared\Requests\SortDirection;
use PHPUnit\Framework\TestCase;

class GridRequestTest extends TestCase
{
	public function testToArrayIncludesSortAndFilter(): void
	{
		$request = GridRequest::create(
			offset: 10,
			limit: 5,
			sorts: [Sort::create('name', SortDirection::Ascending)],
			filters: [Filter::create('active', FilterOperator::Equal, '1')],
		);

		self::assertSame([
			'offset' => 10,
			'limit' => 5,
			'sort' => ['name' => 'asc'],
			'filter' => ['active' => ['eq' => '1']],
		], $request->toArray());
	}

	public function testToArrayOmitsSortAndFilterWhenEmpty(): void
	{
		$request = GridRequest::create();

		self::assertSame([
			'offset' => 0,
			'limit' => 20,
		], $request->toArray());
	}
}
