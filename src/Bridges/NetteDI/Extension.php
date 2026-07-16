<?php declare(strict_types = 1);

namespace Satanio\SdkSkeleton\Bridges\NetteDI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Satanio\SdkSkeleton\Client;
use Satanio\SdkSkeleton\SdkSkeleton;
use stdClass;

/**
 * @property stdClass $config
 */
class Extension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'endpoint' => Expect::string()->dynamic(),
			'signingKey' => Expect::string()->dynamic(),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$client = $builder->addDefinition($this->prefix('client'))
			->setFactory(Client::class)
			->setArguments([
				'endpoint' => $this->config->endpoint,
				'signingKey' => $this->config->signingKey,
			]);

		$builder->addDefinition($this->prefix('service'))
			->setFactory(SdkSkeleton::class, [$client]);
	}

}
