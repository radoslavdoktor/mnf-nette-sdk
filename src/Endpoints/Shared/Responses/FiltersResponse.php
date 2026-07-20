<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Responses;

use Mnf\NetteSdk\Endpoints\IResponse;
use Mnf\NetteSdk\Endpoints\ResponseList;
use Mnf\NetteSdk\Exceptions\ServerException;

class FiltersResponse implements IResponse
{
	/**
	 * @param list<FilterDefinition> $filters
	 */
	private function __construct(
		private readonly array $filters,
	)
	{
	}

	/**
	 * @param array<array-key, mixed> $data
	 * @throws ServerException
	 */
	public static function fromArray(array $data): self
	{
		return new self(ResponseList::parse($data['filters'] ?? null, FilterDefinition::class));
	}

	/**
	 * @return list<FilterDefinition>
	 */
	public function getFilters(): array
	{
		return $this->filters;
	}
}
