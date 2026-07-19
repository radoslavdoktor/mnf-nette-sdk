<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Exceptions;

use Throwable;

trait HasErrorContext
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
}
