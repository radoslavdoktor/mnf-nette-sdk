<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use Throwable;

class InvalidArgumentException extends LogicException
{
	public static function emptyPrivateKey(): self
	{
		return new self('Parameter \'privateKey\' cannot be empty.', 0);
	}

	public static function invalidPrivateKey(Throwable|null $previous = null): self
	{
		return new self('Parameter \'privateKey\' must be a base64-encoded 64-byte Ed25519 secret key.', 0, $previous);
	}
}
