<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Requests;

enum SortDirection: string
{
	case Ascending = 'asc';
	case Descending = 'desc';
}
