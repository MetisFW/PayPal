<?php

namespace MetisFW\PayPal\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
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
    $config = $this->getConfig($this->defaults);

    Validators::assertField($config, 'clientId');
    Validators::assertField($config, 'secret');
    Validators::assertField($config, 'sdkConfig', 'array');

    $builder->addDefinition($this->prefix('simplePaymentOperationFactory'))
      ->setImplement('MetisFW\PayPal\Payment\SimplePaymentOperationFactory');

    $builder->addDefinition($this->prefix('plainPaymentOperationFactory'))
      ->setImplement('MetisFW\PayPal\Payment\PlainPaymentOperationFactory');

    $builder->addDefinition($this->prefix('credentials'))
      ->setClass('PayPal\Auth\OAuthTokenCredential', array($config['clientId'], $config['secret']));

    $builder->addDefinition($this->prefix('apiContext'))
      ->setClass('PayPal\Rest\ApiContext', array($this->prefix('@credentials')));

    $builder->addDefinition($this->prefix('PayPal'))
      ->setClass('MetisFW\PayPal\PayPalContext', array($this->prefix('@apiContext')))
      ->addSetup('setConfig', array($config['sdkConfig']))
      ->addSetup('setCurrency', array($config['currency']))
      ->addSetup('setGaTrackingEnabled', array($config['gaTrackingEnabled']));

    $builder->addDefinition($this->prefix('basicNotificationOperationFactory'))
      ->setImplement('MetisFW\PayPal\Notification\BasicNotificationOperationFactory');
  }

  /**
   * @param Configurator $configurator
   */
  public static function register(Configurator $configurator) {
    $configurator->onCompile[] = function ($config, Compiler $compiler) {
      $compiler->addExtension('payPal', new PayPalExtension());
    };
  }

}
