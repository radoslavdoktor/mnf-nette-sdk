<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Endpoints\Shared\Responses;

enum FilterType: string
{
	case FreeText = 'freeText';
	case Select = 'select';
	case Multiselect = 'multiselect';
}
