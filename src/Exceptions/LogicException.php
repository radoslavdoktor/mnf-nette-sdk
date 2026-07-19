<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

abstract class LogicException extends \LogicException implements Exception
{
	use HasErrorContext;
}
