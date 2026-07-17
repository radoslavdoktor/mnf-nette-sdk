<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton\Exceptions;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use JsonException;
use Throwable;

abstract class Exception extends \Exception
{
	protected mixed $context = null;

	protected function __construct(
		string $message,
		int $code,
		Throwable|null $previous = null,
		mixed $context = null,
	)
	{
		parent::__construct($message, $code, $previous);

		$this->context = $context;
	}

	public function getContext(): mixed
	{
		return $this->context;
	}

	/**
	 * @return array{string, int, Throwable, mixed}
	 */
	protected static function parseBadResponseException(BadResponseException $e): array
	{
		$message = null;
		$context = null;

		if ($e->getResponse() instanceof Response) {
			try {
				/** @var array<string, mixed> $jsonResponse */
				$jsonResponse = \json_decode($e->getResponse()->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
				$message = isset($jsonResponse['message']) && \is_string($jsonResponse['message'])
					? $jsonResponse['message']
					: null;
				$context = $jsonResponse['context'] ?? null;
			} catch (JsonException $jsonException) {
				// do nothing
			}
		}

		return [$message ?? $e->getMessage(), $e->getCode(), $e, $context];
	}
}
