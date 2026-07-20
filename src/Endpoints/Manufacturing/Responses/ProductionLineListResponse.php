<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing\Responses;

class ProductionLineListResponse
{
	/**
	 * @param list<ProductionLineItem> $items
	 */
	private function __construct(
		private readonly array $items,
		private readonly int $totalCount,
	)
	{
	}

	/**
	 * @param list<ProductionLineItem> $items
	 */
	public static function create(array $items, int $totalCount): self
	{
		return new self($items, $totalCount);
	}

	/**
	 * @return list<ProductionLineItem>
	 */
	public function getItems(): array
	{
		return $this->items;
	}

	public function getTotalCount(): int
	{
		return $this->totalCount;
	}
}
