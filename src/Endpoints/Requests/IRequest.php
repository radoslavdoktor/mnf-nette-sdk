<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton\Endpoints\Requests;

interface IRequest
{
	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array;
}
