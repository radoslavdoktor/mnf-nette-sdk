<?php declare(strict_types = 1);

namespace Satanio\SdkSkeleton\Endpoints\Requests;

class ExampleRequest implements IRequest
{

	private string $input;

	public function setInput(string $input): self
	{
		$this->input = $input;

		return $this;
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
