<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use Throwable;

class InvalidArgumentException extends LogicException
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

	public static function emptyPrivateKey(): self
	{
		return new self('Parameter \'privateKey\' cannot be empty.', 0);
	}

	public static function invalidPrivateKey(Throwable|null $previous = null): self
	{
		return new self('Parameter \'privateKey\' must be a base64-encoded 64-byte Ed25519 secret key.', 0, $previous);
	}
}
