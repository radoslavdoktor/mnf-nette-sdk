# SDK Skeleton

Skeleton for Satanio PHP SDKs: a Guzzle-based HTTP client with JWT (HMAC-SHA512) bearer auth,
a typed exception hierarchy, a layered endpoint/request/response structure, and an optional
Nette DI bridge.

Rename `Satanio\SdkSkeleton\SdkSkeleton` to your actual domain service, and replace
`Endpoints\ExampleEndpoint` (plus its `ExampleRequest`/`ExampleResponse`) with your real
endpoints, following the same pattern:

- `SdkSkeleton` is a thin facade — one method per SDK operation, each delegating to an endpoint.
- Each endpoint extends `Endpoints\BaseEndpoint` and holds the `Client`.
- Each request implements `Endpoints\Requests\IRequest` (`toArray(): array`), has a private
  constructor, and is built via a named static factory (e.g. `ExampleRequest::create(...)`).
- Each response implements `Endpoints\Responses\IResponse` (`fromArray(array $data): self`),
  has a private constructor, and exposes typed getters.

Point `Client::sendRequest()` calls at your real endpoints.

## Installation

```shell
composer config repositories.satanio/nette-sdk-skeleton-sdk vcs https://github.com/satanio/nette-sdk-skeleton-sdk
composer require satanio/nette-sdk-skeleton-sdk
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

$request = Satanio\SdkSkeleton\Endpoints\Requests\ExampleRequest::create('some input');

$response = $this->sdk->example($request);

$output = $response->getOutput();
```

## Exceptions

| Exception | When |
|---|---|
| `Satanio\SdkSkeleton\Exceptions\ClientException` | 4xx response or malformed JSON |
| `Satanio\SdkSkeleton\Exceptions\ServerException` | 5xx response or missing payload keys |
| `Satanio\SdkSkeleton\Exceptions\InvalidArgumentException` | invalid constructor arguments (e.g. empty `signingKey`) |

`ClientException` and `ServerException` both expose `getContext(): mixed` with additional error
detail parsed from the API response. All three share named static factories (e.g.
`ServerException::payloadError()`, `InvalidArgumentException::emptySigningKey()`) instead of
public constructors — build them through those, not `new`.

HTTP status codes used by these factories are defined on the `Exceptions\HttpStatusCode` enum.

Server responses are expected in the envelope `{ "payload": { ... } }`, and errors as
`{ "message": "...", "context": <any> }`.

## Local development

No PHP/Composer install needed locally — a `Dockerfile` + `docker-compose.yml` provide a
tooling container. There's no long-running service to keep up, so use `run --rm` for
one-off commands rather than `up -d` + `exec`:

```shell
docker compose run --rm php composer install
docker compose run --rm php vendor/bin/ecs --config=ecs.php --fix
docker compose run --rm php vendor/bin/phpstan analyse
docker compose run --rm php vendor/bin/phpunit
```

Or run all quality checks at once:

```shell
docker compose run --rm php composer qa
```
