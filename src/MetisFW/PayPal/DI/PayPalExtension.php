<?php declare(strict_types=1);

namespace MetisFW\PayPal\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class PayPalExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = (array) $this->getConfig();

		$builder->addFactoryDefinition($this->prefix('simplePaymentOperationFactory'))
			->setImplement('MetisFW\PayPal\Payment\SimplePaymentOperationFactory');

		$builder->addFactoryDefinition($this->prefix('plainPaymentOperationFactory'))
			->setImplement('MetisFW\PayPal\Payment\PlainPaymentOperationFactory');

		$builder->addDefinition($this->prefix('credentials'))
			->setType('PayPal\Auth\OAuthTokenCredential')
			->setArguments([$config['clientId'], $config['secret']]);

		$builder->addDefinition($this->prefix('apiContext'))
			->setType('PayPal\Rest\ApiContext')
			->setArguments([$this->prefix('@credentials')]);

		$paypal = $builder->addDefinition($this->prefix('PayPal'))
			->setType('MetisFW\PayPal\PayPalContext')
			->setArguments([$this->prefix('@apiContext')])
			->addSetup('setConfig', [(array) $config['sdkConfig']])
			->addSetup('setCurrency', [$config['currency']])
			->addSetup('setGaTrackingEnabled', [$config['gaTrackingEnabled']]);

		if ($config['experienceProfileId'] !== null) {
			$paypal->addSetup('setExperienceProfileId', [$config['experienceProfileId']]);
		}
	}

	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('payPal', new PayPalExtension());
		};
	}

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'currency' => Expect::string('CZK'),
			'gaTrackingEnabled' => Expect::bool(true),
			'experienceProfileId' => Expect::string(),
			'clientId' => Expect::string()->required(),
			'secret' => Expect::string()->required(),
			'sdkConfig' => Expect::structure([
				'mode' => Expect::anyOf('sandbox', 'live')->required(),
				'log.LogEnabled' => Expect::bool(),
				'log.FileName' => Expect::string(),
				'log.LogLevel' => Expect::anyOf('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'),
				'cache.enabled' => Expect::bool(true),
				'cache.FileName' => Expect::string(),
			]),
		]);
	}

}
