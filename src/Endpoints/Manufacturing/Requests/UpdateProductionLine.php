<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing\Requests;

use Mnf\NetteSdk\Endpoints\IRequest;

class UpdateProductionLine implements IRequest
{
	private function __construct(
		private readonly string $name,
		private readonly string|null $description,
		private readonly bool $active,
		private readonly string|null $inputPositionId,
		private readonly string|null $outputPositionId,
		private readonly string|null $warehouseId,
	)
	{
	}

	public static function create(
		string $name,
		string|null $description = null,
		bool $active = true,
		string|null $inputPositionId = null,
		string|null $outputPositionId = null,
		string|null $warehouseId = null,
	): self
	{
		return new self($name, $description, $active, $inputPositionId, $outputPositionId, $warehouseId);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return [
			'name' => $this->name,
			'description' => $this->description,
			'active' => $this->active,
			'inputPositionId' => $this->inputPositionId,
			'outputPositionId' => $this->outputPositionId,
			'warehouseId' => $this->warehouseId,
		];
	}
}
