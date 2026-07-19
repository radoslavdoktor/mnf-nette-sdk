<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Requests;

interface IRequest
{
	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array;
}
