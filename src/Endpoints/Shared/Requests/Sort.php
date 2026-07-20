<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Requests;

class Sort
{
	private function __construct(
		private readonly string $attribute,
		private readonly SortDirection $direction,
	)
	{
	}

	public static function create(string $attribute, SortDirection $direction): self
	{
		return new self($attribute, $direction);
	}

	public function getAttribute(): string
	{
		return $this->attribute;
	}

	public function getDirection(): SortDirection
	{
		return $this->direction;
	}
}
