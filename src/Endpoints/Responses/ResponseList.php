<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Responses;

use Mnf\NetteSdk\Exceptions\ServerException;

final class ResponseList
{
	/**
	 * @return array<array-key, mixed>
	 * @throws ServerException
	 */
	public static function assertArray(mixed $data): array
	{
		if (!\is_array($data)) {
			throw ServerException::payloadError();
		}

		return $data;
	}

	/**
	 * @template TItem of IResponse
	 * @param class-string<TItem> $itemClass
	 * @return list<TItem>
	 * @throws ServerException
	 */
	public static function parse(mixed $data, string $itemClass): array
	{
		$items = [];

		foreach (self::assertArray($data) as $item) {
			/** @var TItem $parsedItem */
			$parsedItem = $itemClass::fromArray(self::assertArray($item));
			$items[] = $parsedItem;
		}

		return $items;
	}
}
