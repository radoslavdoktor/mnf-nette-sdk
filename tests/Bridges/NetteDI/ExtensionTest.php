<?php declare(strict_types=1);

namespace Tests\Bridges\NetteDI;

use Mnf\NetteSdk\Bridges\NetteDI\Extension;
use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\Endpoints\Requests\ExampleRequest;
use Mnf\NetteSdk\Exceptions\ClientException;
use Mnf\NetteSdk\Exceptions\InvalidArgumentException;
use Mnf\NetteSdk\Exceptions\ServerException;
use Mnf\NetteSdk\MnfSdk;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Client\DummyClient;

class ExtensionTest extends TestCase
{
	/**
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 * @throws ServerException
	 */
	public function testService(): void
	{
		$client = new DummyClient();
		$service = new MnfSdk($client);

		$response = $service->example(ExampleRequest::create('anything'));

		self::assertSame('dummy-output', $response->getOutput());
	}

	public function testExtension(): void
	{
		$loader = new ContainerLoader(\TEMP_DIR, true);
		$class = $loader->load(static function (Compiler $compiler): string|null {
			$compiler->addExtension('mnfSdk', new Extension());
			$compiler->addConfig([
				'mnfSdk' => [
					'endpoint' => 'http://localhost',
					'privateKey' => 'Syc1jJuyaafnDcDjxBA5nPWQyG/F4IF7brnDENdprRZmlq8bjDKCNZNJj4bTjzDAz4SXKn6niU7KaPIMj0UMcg==',
				],
			]);

			return null;
		}, 2);

		/** @var Container $container */
		$container = new $class();

		self::assertInstanceOf(Client::class, $container->getService('mnfSdk.client'));
		self::assertInstanceOf(MnfSdk::class, $container->getService('mnfSdk.service'));
	}
}
