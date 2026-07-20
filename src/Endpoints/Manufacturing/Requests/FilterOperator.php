<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Manufacturing\Requests;

enum FilterOperator: string
{
	case Equal = 'eq';
	case NotEqual = 'neq';
	case Like = 'like';
	case GreaterThan = 'gt';
	case GreaterThanOrEqual = 'gte';
	case LessThan = 'lt';
	case LessThanOrEqual = 'lte';
	case In = 'in';
	case NotIn = 'nin';
	case IsNull = 'null';
	case IsNotNull = 'notnull';
}
