<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Http;

final class Response
{
	/**
	 * @param mixed $body decoded JSON body
	 * @param array<string, array<int, string>> $headers
	 */
	public function __construct(
		public readonly mixed $body,
		private readonly array $headers,
	)
	{
	}

	public function getHeader(string $name): string|null
	{
		foreach ($this->headers as $headerName => $values) {
			if (\strcasecmp($headerName, $name) === 0) {
				return $values[0] ?? null;
			}
		}

		return null;
	}
}
