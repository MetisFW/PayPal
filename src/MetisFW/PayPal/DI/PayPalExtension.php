<?php

namespace MetisFW\PayPal\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers;
use Nette\Utils\Validators;

class PayPalExtension extends CompilerExtension {

  /**
   * @var array
   */
  public $defaults = array(
    'currency' => 'CZK',
    'gaTrackingEnabled' => true
  );

  public function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $config = Helpers::merge($this->getConfig(), $this->defaults);

    Validators::assertField($config, 'clientId');
    Validators::assertField($config, 'secret');
    Validators::assertField($config, 'sdkConfig', 'array');

    $builder->addFactoryDefinition($this->prefix('simplePaymentOperationFactory'))
      ->setImplement('MetisFW\PayPal\Payment\SimplePaymentOperationFactory');

    $builder->addFactoryDefinition($this->prefix('plainPaymentOperationFactory'))
      ->setImplement('MetisFW\PayPal\Payment\PlainPaymentOperationFactory');

    $builder->addDefinition($this->prefix('credentials'))
      ->setType('PayPal\Auth\OAuthTokenCredential')
	  ->setArguments(array($config['clientId'], $config['secret']));

    $builder->addDefinition($this->prefix('apiContext'))
      ->setType('PayPal\Rest\ApiContext')
	  ->setArguments(array($this->prefix('@credentials')));

    $paypal = $builder->addDefinition($this->prefix('PayPal'))
      ->setType('MetisFW\PayPal\PayPalContext')
	  ->setArguments(array($this->prefix('@apiContext')))
      ->addSetup('setConfig', array($config['sdkConfig']))
      ->addSetup('setCurrency', array($config['currency']))
      ->addSetup('setGaTrackingEnabled', array($config['gaTrackingEnabled']));

    if (isset($config['experienceProfileId'])) {
      $paypal->addSetup('setExperienceProfileId', array($config['experienceProfileId']));
    }
  }

  public static function register(Configurator $configurator) {
    $configurator->onCompile[] = function ($config, Compiler $compiler) {
      $compiler->addExtension('payPal', new PayPalExtension());
    };
  }

}
