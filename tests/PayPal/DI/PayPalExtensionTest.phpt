<?php

namespace MetisFWTests\PayPal\DI;

use MetisFW\PayPal\DI\PayPalExtension;
use MetisFW\PayPal\Payment\PlainPaymentOperation;
use MetisFW\PayPal\Payment\SimplePaymentOperation;
use Nette\Configurator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__.'/../../bootstrap.php';

class PayPalExtensionTest extends TestCase {

  public function testExtensionCreated() {
    $config = new Configurator();
    $config->setTempDirectory(TEMP_DIR);
    $config->addParameters(array('container' => array('class' => 'SystemContainer_'.md5(TEMP_DIR))));
    PayPalExtension::register($config);
    $config->addConfig(__DIR__.'/../../paypal.config.neon');

    $container = $config->createContainer();
    $paypal = $container->getByType('MetisFW\PayPal\PayPalContext');

    Assert::notEqual(null, $paypal);

    $simpleOperationFactory = $container->getByType('MetisFW\PayPal\Payment\SimplePaymentOperationFactory');
    $operation = $simpleOperationFactory->create('Coffee', 10);
    Assert::true($operation instanceof SimplePaymentOperation);

    $plainOperationFactory = $container->getByType('MetisFW\PayPal\Payment\PlainPaymentOperationFactory');
    $operation = $plainOperationFactory->create(array());
    Assert::true($operation instanceof PlainPaymentOperation);
  }

}

\run(new PayPalExtensionTest());
