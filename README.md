# SDK Skeleton

Skeleton for Satanio PHP SDKs: a Guzzle-based HTTP client with JWT (HMAC-SHA512) bearer auth,
a typed exception hierarchy, a layered endpoint/request/response structure, and an optional
Nette DI bridge.

Rename `Satanio\SdkSkeleton\SdkSkeleton` to your actual domain service, and replace
`Endpoints\ExampleEndpoint` (plus its `ExampleRequest`/`ExampleResponse`) with your real
endpoints, following the same pattern:

- `SdkSkeleton` is a thin facade — one method per SDK operation, each delegating to an endpoint.
- Each endpoint extends `Endpoints\BaseEndpoint` and holds the `Client`.
- Each request implements `Endpoints\Requests\IRequest` (`toArray(): array`) and is built with
  fluent setters.
- Each response implements `Endpoints\Responses\IResponse` (`__construct(array $data)`) and
  exposes typed getters.

Point `Client::sendRequest()` calls at your real endpoints.

## Installation

```shell
composer require satanio/sdk-skeleton
```

## Configuration

Register the extension in your Nette DI config:

```neon
extensions:
    sdkSkeleton: Satanio\SdkSkeleton\Bridges\NetteDI\Extension

sdkSkeleton:
    endpoint: https://your-api.com
    signingKey: your-hmac-secret
```

- `endpoint` — base URL of the API
- `signingKey` — shared HMAC secret used to sign JWT requests
- `autowired` — whether the `Client` and `SdkSkeleton` services are autowired (default `true`);
  set to `false` when registering more than one SDK extension to avoid autowiring ambiguity

Both `endpoint` and `signingKey` support environment variable resolution via `%env.VAR%`.

Without Nette DI, construct the client directly:

```php
$client = new Satanio\SdkSkeleton\Client('https://your-api.com', 'your-hmac-secret');
$sdk = new Satanio\SdkSkeleton\SdkSkeleton($client);
```

## Usage

```php
/** @inject */
public Satanio\SdkSkeleton\SdkSkeleton $sdk;

$request = (new Satanio\SdkSkeleton\Endpoints\Requests\ExampleRequest())
	->setInput('some input');

$response = $this->sdk->example($request);

$output = $response->getOutput();
```

## Exceptions

| Exception | When |
|---|---|
| `Satanio\SdkSkeleton\Exceptions\ClientException` | 4xx response or malformed JSON |
| `Satanio\SdkSkeleton\Exceptions\ServerException` | 5xx response or missing payload keys |

Both expose `getContext(): mixed` with additional error detail parsed from the API response.

Server responses are expected in the envelope `{ "payload": { ... } }`, and errors as
`{ "message": "...", "context": <any> }`.
