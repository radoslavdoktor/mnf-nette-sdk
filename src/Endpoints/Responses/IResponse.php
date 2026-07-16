<?php declare(strict_types = 1);

namespace Satanio\SdkSkeleton\Endpoints\Responses;

interface IResponse
{

	/**
	 * @param array<array-key, mixed> $data
	 */
	public function __construct(array $data);

}
