<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Requests;

class Filter
{
	private function __construct(
		private readonly string $attribute,
		private readonly FilterOperator $operator,
		private readonly string $value,
	)
	{
	}

	public static function create(
		string $attribute,
		FilterOperator $operator,
		string $value,
	): self
	{
		return new self($attribute, $operator, $value);
	}

	public function getAttribute(): string
	{
		return $this->attribute;
	}

	public function getOperator(): FilterOperator
	{
		return $this->operator;
	}

	public function getValue(): string
	{
		return $this->value;
	}
}
