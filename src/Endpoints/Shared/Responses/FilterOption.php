<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Responses;

use Mnf\NetteSdk\Endpoints\Responses\IResponse;
use Mnf\NetteSdk\Exceptions\ServerException;

class FilterOption implements IResponse
{
	private function __construct(
		private readonly string $name,
		private readonly string $value,
	)
	{
	}

	/**
	 * @param array<array-key, mixed> $data
	 * @throws ServerException
	 */
	public static function fromArray(array $data): self
	{
		$name = $data['name'] ?? null;
		$value = $data['value'] ?? null;

		if (!\is_string($name) || !\is_string($value)) {
			throw ServerException::payloadError();
		}

		return new self($name, $value);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getValue(): string
	{
		return $this->value;
	}
}
