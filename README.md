# SDK Skeleton

Skeleton for Satanio PHP SDKs: a Guzzle-based HTTP client with JWT (HMAC-SHA512) bearer auth,
a typed exception hierarchy, and an optional Nette DI bridge.

Rename `Satanio\SdkSkeleton\SdkSkeleton` and its `example()` method to your actual domain
service, and point `Client::sendRequest()` calls at your real endpoints.

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

Both values support environment variable resolution via `%env.VAR%`.

Without Nette DI, construct the client directly:

```php
$client = new Satanio\SdkSkeleton\Client('https://your-api.com', 'your-hmac-secret');
$sdk = new Satanio\SdkSkeleton\SdkSkeleton($client);
```

## Usage

```php
/** @inject */
public Satanio\SdkSkeleton\SdkSkeleton $sdk;

$output = $this->sdk->example('some input');
```

## Exceptions

| Exception | When |
|---|---|
| `Satanio\SdkSkeleton\Exceptions\ClientException` | 4xx response or malformed JSON |
| `Satanio\SdkSkeleton\Exceptions\ServerException` | 5xx response or missing payload keys |

Both expose `getContext(): mixed` with additional error detail parsed from the API response.

Server responses are expected in the envelope `{ "payload": { ... } }`, and errors as
`{ "message": "...", "context": <any> }`.
