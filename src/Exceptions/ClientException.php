<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use JsonException;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ClientException extends RuntimeException
{
	public static function createFromResponse(ResponseInterface $response): self
	{
		return new self(...self::parseFromResponse($response));
	}

	public static function invalidJsonResponse(JsonException $e): self
	{
		return new self($e->getMessage(), $e->getCode(), $e);
	}
}
