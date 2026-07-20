<?php declare(strict_types=1);

namespace Mnf\NetteSdk\Bridges\NetteDI;

use Mnf\NetteSdk\Client;
use Mnf\NetteSdk\ManufacturingApi;
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
			'baseUri' => Expect::string()->dynamic(),
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
				'baseUri' => $this->config->baseUri,
				'privateKey' => $this->config->privateKey,
			])
			->setAutowired((bool)$this->config->autowired);

		$builder->addDefinition($this->prefix('service'))
			->setFactory(ManufacturingApi::class, [$client])
			->setAutowired((bool)$this->config->autowired);
	}
}
