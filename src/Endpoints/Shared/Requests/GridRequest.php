<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Requests;

use Mnf\NetteSdk\Endpoints\IRequest;

class GridRequest implements IRequest
{
	/**
	 * @param list<Sort> $sorts
	 * @param list<Filter> $filters
	 */
	private function __construct(
		private readonly int $offset,
		private readonly int $limit,
		private readonly array $sorts,
		private readonly array $filters,
	)
	{
	}

	/**
	 * @param list<Sort> $sorts
	 * @param list<Filter> $filters
	 */
	public static function create(
		int $offset = 0,
		int $limit = 20,
		array $sorts = [],
		array $filters = [],
	): self
	{
		return new self($offset, $limit, $sorts, $filters);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$query = [
			'offset' => $this->offset,
			'limit' => $this->limit,
		];

		foreach ($this->sorts as $sort) {
			$query['sort'][$sort->getAttribute()] = $sort->getDirection()->value;
		}

		foreach ($this->filters as $filter) {
			$query['filter'][$filter->getAttribute()][$filter->getOperator()->value] = $filter->getValue();
		}

		return $query;
	}
}
