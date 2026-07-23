# MNF Nette SDK

A Symfony HttpClient-based HTTP client for the MNF API, with JWT (EdDSA/Ed25519) bearer auth, a typed
exception hierarchy, a layered endpoint/request/response structure, and an optional Nette DI
bridge.

Endpoints are grouped by MNF domain under `Endpoints\<Domain>`, e.g. `Endpoints\Manufacturing`.
Vocabulary generic across domains (pagination, sorting, filtering) lives in `Endpoints\Shared`
instead of being duplicated per domain:

- `ManufacturingApi` (and one facade class per domain) is a thin facade — one method per SDK
  operation, each delegating to an endpoint.
- Each endpoint extends `Endpoints\BaseEndpoint` and holds the `Client`.
- Each request implements `Endpoints\Requests\IRequest` (`toArray(): array`), has a private
  constructor, and is built via a named static factory (e.g. `GridRequest::create(...)`).
- Each response has a private constructor and exposes typed getters; most implement
  `Endpoints\Responses\IResponse` (`fromArray(array $data): self`) — a response that needs data
  from outside the JSON body too (e.g. a header) uses a plain `create()` factory instead.
- `Endpoints\Shared\Requests\GridRequest` (offset/limit/`Sort[]`/`Filter[]`) and
  `Endpoints\Shared\Responses\FiltersResponse` are reused by every grid-style list endpoint,
  across every domain.

`Client::sendRequest()` returns a `Http\Response`, exposing the decoded JSON body (`->body`,
`mixed` — a list for grid endpoints, a map otherwise) and response headers (`->getHeader(string): ?string`).

## Installation

```shell
composer config repositories.vilgain/mnf-nette-sdk vcs https://github.com/radoslavdoktor/mnf-nette-sdk
composer require vilgain/mnf-nette-sdk
```

## Configuration

Register the extension in your Nette DI config:

```neon
extensions:
    manufacturingApi: Mnf\NetteSdk\Bridges\NetteDI\Extension

manufacturingApi:
    baseUri: https://your-api.com
    privateKey: '%env.MNF_JWT_PRIVATE_KEY%'
```

- `baseUri` — base URL of the API
- `privateKey` — base64-encoded Ed25519 private key used to sign JWT requests; the MNF API
  verifies signatures against the corresponding public key, so only this key's holder can
  authenticate — never commit it, keep it in your secrets store
- `autowired` — whether the `Client` and `ManufacturingApi` services are autowired (default
  `true`); set to `false` when registering more than one SDK extension to avoid autowiring
  ambiguity

Both `baseUri` and `privateKey` support environment variable resolution via `%env.VAR%`.

Without Nette DI, construct the client directly:

```php
$client = new Mnf\NetteSdk\Client('https://your-api.com', $privateKey);
$api = new Mnf\NetteSdk\ManufacturingApi($client);
```

## Identity

Every request is signed with a JWT carrying an optional `sub` (subject) claim and `roles` claim,
identifying who the SDK is acting as. Rather than passing `$subject`/`$roles` to every endpoint
method, set them once via `Client::withIdentity()` (or the equivalent `ManufacturingApi::withIdentity()`,
which just forwards to its `Client`). Both return an independent clone — the original is left
untouched — so it's cheap to call per request/per user without affecting other holders of the
original instance:

```php
$client = $client->withIdentity($user->getId(), $user->getRoles());
// or, via the facade:
$api = $api->withIdentity($user->getId(), $user->getRoles());
```

Omit the call (or pass `null`/`[]`) to sign requests with no subject/roles claims.

## Usage

```php
/** @inject */
public Mnf\NetteSdk\ManufacturingApi $manufacturingApi;

use Mnf\NetteSdk\Endpoints\Shared\Requests\Filter;
use Mnf\NetteSdk\Endpoints\Shared\Requests\FilterOperator;
use Mnf\NetteSdk\Endpoints\Shared\Requests\GridRequest;

$this->manufacturingApi = $this->manufacturingApi->withIdentity($user->getId(), $user->getRoles());

$request = GridRequest::create(
    offset: 0,
    limit: 20,
    filters: [Filter::create('active', FilterOperator::Equal, '1')],
);

$response = $this->manufacturingApi->getProductionLines($request);

$totalCount = $response->getTotalCount(); // from the X-Count response header
foreach ($response->getItems() as $item) {
    $item->getId();
    $item->getName();
}

$filters = $this->manufacturingApi->getProductionLineFilters();

use Mnf\NetteSdk\Endpoints\Manufacturing\Requests\ProductionLineRequest;

$item = $this->manufacturingApi->getProductionLine($id);

$created = $this->manufacturingApi->createProductionLine(
    ProductionLineRequest::create(name: 'Line A', description: 'desc', active: true),
);

$updated = $this->manufacturingApi->updateProductionLine(
    $id,
    ProductionLineRequest::create(name: 'Line A', active: false),
);

$this->manufacturingApi->deleteProductionLine($id);
```

## Exceptions

| Exception | When |
|---|---|
| `Mnf\NetteSdk\Exceptions\ClientException` | 4xx response or malformed JSON |
| `Mnf\NetteSdk\Exceptions\ServerException` | 5xx response, missing payload keys, or a transport-level failure (DNS, connection refused, timeout) |
| `Mnf\NetteSdk\Exceptions\InvalidArgumentException` | invalid constructor arguments (e.g. empty `privateKey`) |

`ClientException` and `ServerException` both expose `getContext(): mixed`, populated with the
first entry of the error response's `error` array (see below). All three share named static
factories (e.g. `ServerException::payloadError()`, `InvalidArgumentException::emptyPrivateKey()`)
instead of public constructors — build them through those, not `new`.

HTTP status codes used by these factories are defined on the `Exceptions\HttpStatusCode` enum.

Successful responses are the bare response DTO at the top level (no envelope) — e.g.
`{ "id": "...", "name": "..." }`, or for grid/list endpoints a bare JSON array with the total row
count in an `X-Count` response header rather than the body. Error responses use
`{ "error": [{ "message": "...", "path"?: "...", "code"?: "..." }] }` — `error` is always an
array; only the first entry is surfaced.

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
