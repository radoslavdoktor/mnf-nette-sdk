<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing\Requests;

enum SortDirection: string
{
	case Ascending = 'asc';
	case Descending = 'desc';
}
