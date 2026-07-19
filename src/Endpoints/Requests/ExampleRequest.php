<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Requests;

class ExampleRequest implements IRequest
{
	private function __construct(private readonly string $input)
	{
	}

	public static function create(string $input): self
	{
		return new self($input);
	}

	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array
	{
		return [
			'input' => $this->input,
		];
	}
}
