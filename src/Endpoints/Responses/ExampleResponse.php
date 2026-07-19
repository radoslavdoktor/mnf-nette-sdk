<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Responses;

use Mnf\NetteSdk\Exceptions\ServerException;

class ExampleResponse implements IResponse
{
	private function __construct(private readonly string $output)
	{
	}

	/**
	 * @param array<array-key, mixed> $data
	 * @throws ServerException
	 */
	public static function fromArray(array $data): self
	{
		$output = $data['output'] ?? null;

		if (!\is_string($output)) {
			throw ServerException::payloadError();
		}

		return new self($output);
	}

	public function getOutput(): string
	{
		return $this->output;
	}
}
