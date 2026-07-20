<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing\Responses;

use Mnf\NetteSdk\Endpoints\Responses\IResponse;
use Mnf\NetteSdk\Exceptions\ServerException;

class ProductionLineFilterDefinition implements IResponse
{
	/**
	 * @param list<FilterOption> $options
	 */
	private function __construct(
		private readonly string $attribute,
		private readonly FilterType $type,
		private readonly array $options,
	)
	{
	}

	/**
	 * @param array<array-key, mixed> $data
	 * @throws ServerException
	 */
	public static function fromArray(array $data): self
	{
		$attribute = $data['attribute'] ?? null;
		$type = \is_string($data['type'] ?? null) ? FilterType::tryFrom($data['type']) : null;
		$rawOptions = $data['options'] ?? null;

		if (!\is_string($attribute) || $type === null || !\is_array($rawOptions)) {
			throw ServerException::payloadError();
		}

		$options = [];

		foreach ($rawOptions as $rawOption) {
			if (!\is_array($rawOption)) {
				throw ServerException::payloadError();
			}

			$options[] = FilterOption::fromArray($rawOption);
		}

		return new self($attribute, $type, $options);
	}

	public function getAttribute(): string
	{
		return $this->attribute;
	}

	public function getType(): FilterType
	{
		return $this->type;
	}

	/**
	 * @return list<FilterOption>
	 */
	public function getOptions(): array
	{
		return $this->options;
	}
}
