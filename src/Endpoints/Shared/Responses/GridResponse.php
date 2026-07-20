<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Responses;

/**
 * @template TItem
 */
class GridResponse
{
	/**
	 * @param list<TItem> $items
	 */
	private function __construct(
		private readonly array $items,
		private readonly int $totalCount,
	)
	{
	}

	/**
	 * @template TCreateItem
	 * @param list<TCreateItem> $items
	 * @return self<TCreateItem>
	 */
	public static function create(array $items, int $totalCount): self
	{
		return new self($items, $totalCount);
	}

	/**
	 * @return list<TItem>
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
