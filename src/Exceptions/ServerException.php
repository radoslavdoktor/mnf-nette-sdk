<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ServerException extends RuntimeException
{
	public static function createFromResponse(ResponseInterface $response): self
	{
		return new self(...self::parseFromResponse($response));
	}

	public static function createFromTransportException(TransportExceptionInterface $e): self
	{
		return new self($e->getMessage(), 0, $e);
	}

	public static function payloadError(): self
	{
		return new self('Payload error', HttpStatusCode::NotFound->value);
	}
}
