<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton\Endpoints\Responses;

use Satanio\SdkSkeleton\Exceptions\ServerException;

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
