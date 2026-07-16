<?php declare(strict_types = 1);

namespace Satanio\SdkSkeleton\Endpoints\Responses;

use Satanio\SdkSkeleton\Exceptions\ServerException;

class ExampleResponse implements IResponse
{

	private string $output;

	/**
	 * @param array<array-key, mixed> $data
	 */
	public function __construct(array $data)
	{
		$output = $data['output'] ?? null;

		if (!is_string($output)) {
			throw new ServerException('Payload error', 404);
		}

		$this->output = $output;
	}

	public function getOutput(): string
	{
		return $this->output;
	}

}
