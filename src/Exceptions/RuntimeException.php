<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use JsonException;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class RuntimeException extends \RuntimeException implements Exception
{
	use HasErrorContext;

	/**
	 * @return array{string, int, null, mixed}
	 */
	protected static function parseFromResponse(ResponseInterface $response): array
	{
		$message = null;
		$context = null;
		$statusCode = $response->getStatusCode();

		try {
			/** @var array<string, mixed> $jsonResponse */
			$jsonResponse = \json_decode($response->getContent(false), true, 512, \JSON_THROW_ON_ERROR);

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

		return [$message ?? \sprintf('HTTP error %d', $statusCode), $statusCode, null, $context];
	}
}
