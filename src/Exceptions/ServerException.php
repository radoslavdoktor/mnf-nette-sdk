<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use GuzzleHttp\Exception\BadResponseException;

class ServerException extends RuntimeException
{
	public static function createFromBadResponseException(BadResponseException $e): self
	{
		return new self(...self::parseBadResponseException($e));
	}

	public static function payloadError(): self
	{
		return new self('Payload error', HttpStatusCode::NotFound->value);
	}
}
