<?php declare(strict_types = 1);

namespace Satanio\SdkSkeleton\Exceptions;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Throwable;

abstract class Exception extends \Exception
{

	protected mixed $context = null;

	final public function __construct(string $message, int $code, ?Throwable $previous = null, mixed $context = null)
	{
		parent::__construct($message, $code, $previous);

		$this->context = $context;
	}

	public static function createFromBadResponseException(BadResponseException $e): self
	{
		if ($e->getResponse() instanceof Response) {
			try {
				/** @var array<string, mixed> $jsonResponse */
				$jsonResponse = Json::decode($e->getResponse()->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
				$message = isset($jsonResponse['message']) && is_string($jsonResponse['message'])
					? $jsonResponse['message']
					: null;
				$context = $jsonResponse['context'] ?? null;
			} catch (JsonException $jsonException) {
				// do nothing
			}
		}

		return new static($message ?? $e->getMessage(), $e->getCode(), $e, $context ?? null);
	}

	public function getContext(): mixed
	{
		return $this->context;
	}

}
