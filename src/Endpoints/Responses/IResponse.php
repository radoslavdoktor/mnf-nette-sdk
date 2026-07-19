<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Responses;

interface IResponse
{
	/**
	 * @param array<array-key, mixed> $data
	 */
	public static function fromArray(array $data): self;
}
