<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing\Responses;

use Mnf\NetteSdk\Endpoints\IResponse;
use Mnf\NetteSdk\Exceptions\ServerException;

class ProductionLine implements IResponse
{
	private function __construct(
		private readonly string $id,
		private readonly string $name,
		private readonly string|null $description,
		private readonly bool $active,
		private readonly string|null $inputPositionId,
		private readonly string|null $outputPositionId,
		private readonly string|null $warehouseId,
	)
	{
	}

	/**
	 * @param array<array-key, mixed> $data
	 * @throws ServerException
	 */
	public static function fromArray(array $data): self
	{
		$id = $data['id'] ?? null;
		$name = $data['name'] ?? null;
		$description = $data['description'] ?? null;
		$active = $data['active'] ?? null;
		$inputPositionId = $data['inputPositionId'] ?? null;
		$outputPositionId = $data['outputPositionId'] ?? null;
		$warehouseId = $data['warehouseId'] ?? null;

		if (
			!\is_string($id)
			|| !\is_string($name)
			|| ($description !== null && !\is_string($description))
			|| !\is_bool($active)
			|| ($inputPositionId !== null && !\is_string($inputPositionId))
			|| ($outputPositionId !== null && !\is_string($outputPositionId))
			|| ($warehouseId !== null && !\is_string($warehouseId))
		) {
			throw ServerException::payloadError();
		}

		return new self($id, $name, $description, $active, $inputPositionId, $outputPositionId, $warehouseId);
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDescription(): string|null
	{
		return $this->description;
	}

	public function isActive(): bool
	{
		return $this->active;
	}

	public function getInputPositionId(): string|null
	{
		return $this->inputPositionId;
	}

	public function getOutputPositionId(): string|null
	{
		return $this->outputPositionId;
	}

	public function getWarehouseId(): string|null
	{
		return $this->warehouseId;
	}
}
