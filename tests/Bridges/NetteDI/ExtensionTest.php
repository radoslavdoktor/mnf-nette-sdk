<?php declare(strict_types=1);

namespace Tests\Bridges\NetteDI;

use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use PHPUnit\Framework\TestCase;
use Satanio\SdkSkeleton\Bridges\NetteDI\Extension;
use Satanio\SdkSkeleton\Client;
use Satanio\SdkSkeleton\Endpoints\Requests\ExampleRequest;
use Satanio\SdkSkeleton\Exceptions\ClientException;
use Satanio\SdkSkeleton\Exceptions\InvalidArgumentException;
use Satanio\SdkSkeleton\Exceptions\ServerException;
use Satanio\SdkSkeleton\SdkSkeleton;
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
		$service = new SdkSkeleton($client);

		$response = $service->example(ExampleRequest::create('anything'));

		self::assertSame('dummy-output', $response->getOutput());
	}

	public function testExtension(): void
	{
		$loader = new ContainerLoader(\TEMP_DIR, true);
		$class = $loader->load(static function (Compiler $compiler): string|null {
			$compiler->addExtension('sdkSkeleton', new Extension());
			$compiler->addConfig([
				'sdkSkeleton' => [
					'endpoint' => 'http://localhost',
					'signingKey' => 'test-signing-key',
				],
			]);

			return null;
		}, 2);

		/** @var Container $container */
		$container = new $class();

		self::assertInstanceOf(Client::class, $container->getService('sdkSkeleton.client'));
		self::assertInstanceOf(SdkSkeleton::class, $container->getService('sdkSkeleton.service'));
	}
}
