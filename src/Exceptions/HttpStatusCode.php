<?php declare(strict_types=1);

namespace Satanio\SdkSkeleton\Exceptions;

enum HttpStatusCode: int
{
	case BadRequest = 400;
	case Unauthorized = 401;
	case Forbidden = 403;
	case NotFound = 404;
	case Conflict = 409;
	case UnprocessableEntity = 422;
	case TooManyRequests = 429;
	case InternalServerError = 500;
	case BadGateway = 502;
	case ServiceUnavailable = 503;
	case GatewayTimeout = 504;
}
