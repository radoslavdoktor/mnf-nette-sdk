<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use JsonException;
use Throwable;

abstract class RuntimeException extends \RuntimeException implements Exception
{
	use HasErrorContext;

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

				/** @var array<array-key, mixed>|null $errors */
				$errors = \is_array($jsonResponse['error'] ?? null) ? $jsonResponse['error'] : null;

				/** @var array<string, mixed>|null $firstError */
				$firstError = \is_array($errors[0] ?? null) ? $errors[0] : null;

				$message = isset($firstError['message']) && \is_string($firstError['message'])
					? $firstError['message']
					: null;
				$context = $firstError;
			} catch (JsonException $jsonException) {
				// do nothing
			}
		}

		return [$message ?? $e->getMessage(), $e->getCode(), $e, $context];
	}
}
