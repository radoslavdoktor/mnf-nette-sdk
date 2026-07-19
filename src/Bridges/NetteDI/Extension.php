<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Bridges\NetteDI;

use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\MnfSdk;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
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
			'privateKey' => Expect::string()->dynamic(),
			'autowired' => Expect::bool(true)->dynamic(),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$client = $builder->addDefinition($this->prefix('client'))
			->setFactory(Client::class)
			->setArguments([
				'endpoint' => $this->config->endpoint,
				'privateKey' => $this->config->privateKey,
			])
			->setAutowired((bool)$this->config->autowired);

		$builder->addDefinition($this->prefix('service'))
			->setFactory(MnfSdk::class, [$client])
			->setAutowired((bool)$this->config->autowired);
	}
}
