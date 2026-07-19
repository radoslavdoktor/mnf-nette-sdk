<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use GuzzleHttp\Exception\BadResponseException;
use Throwable;

class ServerException extends RuntimeException
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

	public static function createFromBadResponseException(BadResponseException $e): self
	{
		return new self(...self::parseBadResponseException($e));
	}

	public static function payloadError(): self
	{
		return new self('Payload error', HttpStatusCode::NotFound->value);
	}
}
