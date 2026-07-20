<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing\Responses;

use Mnf\NetteSdk\Endpoints\Responses\IResponse;
use Mnf\NetteSdk\Exceptions\ServerException;

class ProductionLineFiltersResponse implements IResponse
{
	/**
	 * @param list<ProductionLineFilterDefinition> $filters
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
		$rawFilters = $data['filters'] ?? null;

		if (!\is_array($rawFilters)) {
			throw ServerException::payloadError();
		}

		$filters = [];

		foreach ($rawFilters as $rawFilter) {
			if (!\is_array($rawFilter)) {
				throw ServerException::payloadError();
			}

			$filters[] = ProductionLineFilterDefinition::fromArray($rawFilter);
		}

		return new self($filters);
	}

	/**
	 * @return list<ProductionLineFilterDefinition>
	 */
	public function getFilters(): array
	{
		return $this->filters;
	}
}
