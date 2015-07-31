<?php

namespace MetisFWTests\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;
use Mockery\MockInterface;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Payment\DummyPaymentOperation;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__.'/../../bootstrap.php';
require_once 'DummyPaymentOperation.php';

class PaymentOperationTest extends TestCase {

  /** @var array */
  private $config;

  /** @var MockInterface */
  private $paymentMock;

  /**
   * This method is called before a test is executed.
   *
   * @return void
   */
  protected function setUp() {
    parent::setUp();
    $this->config = array(
      'clientId' => 'AUqne4ywvozUaSQ1THTZYKFr88bhtA0SS_fXBoJTfeSTIasDBWuXLiLcFlfmSXRfL-kZ3Z5shvNrT6rP',
      'secretId' => 'EDGPDc3a65JBBY7-IKkNak7aGTVTvY-NhJgfhptegSML58fWjfp89U7UKNgGk9UI-UEZ-btfaE2sGST1'
    );
  }

  public function testGetPayment() {
    $apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', array());
    $context = new PayPalContext($apiContext);
    $operation = new DummyPaymentOperation($context);

    $payment = $operation->getPayment();
    Assert::equal('123456789', $payment->transactions[0]->invoice_number);
  }


  public function testCreatePayment() {
    $credentials = new OAuthTokenCredential($this->config['clientId'], $this->config['secretId']);
    $apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', array($credentials))->makePartial();
    $context = new PayPalContext($apiContext);
    $operation = new DummyPaymentOperation($context);
    $payment = $operation->getPayment();

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl('http://localhost/return');
    $redirectUrls->setCancelUrl('http://localhost/cancel');
    $payment->setRedirectUrls($redirectUrls);

    $result = $operation->createPayment($payment);
    Assert::equal('created', $result->getState());
    Assert::notEqual(null, $result->getApprovalLink());
  }

  /**
   * This method is called after a test is executed.
   *
   * @return void
   */
  protected function tearDown() {
    parent::tearDown();
    \Mockery::close();
  }

}

\run(new PaymentOperationTest());


