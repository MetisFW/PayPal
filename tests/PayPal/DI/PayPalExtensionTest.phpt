<?php

namespace MetisFWTests\PayPal\DI;

use MetisFW\PayPal\DI\PayPalExtension;
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
  }

}

\run(new PayPalExtensionTest());
