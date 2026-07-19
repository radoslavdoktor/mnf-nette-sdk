<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use Throwable;

interface Exception extends Throwable
{
	public function getContext(): mixed;
}
