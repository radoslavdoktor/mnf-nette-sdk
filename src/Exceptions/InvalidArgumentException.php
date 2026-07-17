<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton\Exceptions;

use Throwable;

class InvalidArgumentException extends Exception
{
	private function __construct(
		string $message,
		int $code,
		Throwable|null $previous = null,
		mixed $context = null,
	)
	{
		parent::__construct($message, $code, $previous, $context);
	}

	public static function create(
		string $message,
		int $code,
		Throwable|null $previous = null,
		mixed $context = null,
	): self
	{
		return new self($message, $code, $previous, $context);
	}

	public static function emptySigningKey(): self
	{
		return new self('Parameter \'signingKey\' cannot be empty.', 0);
	}
}
