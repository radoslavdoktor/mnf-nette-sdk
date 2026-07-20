<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints;

interface IRequest
{
	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array;
}
