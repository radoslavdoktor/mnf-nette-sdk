<?php declare(strict_types = 1);

namespace Tests\Cases\Bridges\NetteDI;

use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Satanio\SdkSkeleton\Bridges\NetteDI\Extension;
use Satanio\SdkSkeleton\Client;
use Satanio\SdkSkeleton\SdkSkeleton;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;
use Tests\Stubs\Client\DummyClient;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
class ExtensionCase extends TestCase
{

	public function testService(): void
	{
		$client = new DummyClient();
		$service = new SdkSkeleton($client);

		Assert::same('dummy-output', $service->example('anything'));
	}

	public function testExtension(): void
	{
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(static function (Compiler $compiler): string|null {
			$compiler->addExtension('sdkSkeleton', new Extension());
			$compiler->loadConfig(FileMock::create('
			sdkSkeleton:
				endpoint: http://localhost
				signingKey: test-signing-key
			', 'neon'));

			return null;
		}, 2);

		/** @var Container $container */
		$container = new $class();

		Assert::type(Client::class, $container->getService('sdkSkeleton.client'));
		Assert::type(SdkSkeleton::class, $container->getService('sdkSkeleton.service'));
	}

}

(new ExtensionCase())->run();
