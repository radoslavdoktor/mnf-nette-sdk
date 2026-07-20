<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Responses;

use Mnf\NetteSdk\Endpoints\Responses\IResponse;
use Mnf\NetteSdk\Endpoints\Responses\ResponseList;
use Mnf\NetteSdk\Exceptions\ServerException;

class FilterDefinition implements IResponse
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

		if (!\is_string($attribute) || $type === null) {
			throw ServerException::payloadError();
		}

		return new self($attribute, $type, ResponseList::parse($data['options'] ?? null, FilterOption::class));
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
