<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use GuzzleHttp\Exception\BadResponseException;
use JsonException;

class ClientException extends RuntimeException
{
	public static function createFromBadResponseException(BadResponseException $e): self
	{
		return new self(...self::parseBadResponseException($e));
	}

	public static function invalidJsonResponse(JsonException $e): self
	{
		return new self($e->getMessage(), $e->getCode(), $e);
	}
}
