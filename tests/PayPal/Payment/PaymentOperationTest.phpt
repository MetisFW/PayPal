<?php declare(strict_types=1);

namespace Tests\PayPal\Payment;

use MetisFW\PayPal\Payment\PlainPaymentOperation;
use MetisFW\PayPal\Payment\SimplePaymentOperation;
use MetisFW\PayPal\PayPalContext;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Auth\OAuthTokenCredential;
use Tester\Assert;
use Tester\TestCase;
use Tests\PayPal\Helper\TransactionHelper;

require_once __DIR__ . '/../../bootstrap.php';
require_once 'DummyPaymentOperation.php';
require_once __DIR__ . '/../Helper/TransactionHelper.php';

class PaymentOperationTest extends TestCase
{

	/** @var array */
	private $config;

	/**
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->config = [
			'clientId' => 'AUqne4ywvozUaSQ1THTZYKFr88bhtA0SS_fXBoJTfeSTIasDBWuXLiLcFlfmSXRfL-kZ3Z5shvNrT6rP',
			'secretId' => 'EDGPDc3a65JBBY7-IKkNak7aGTVTvY-NhJgfhptegSML58fWjfp89U7UKNgGk9UI-UEZ-btfaE2sGST1'
		];
	}

	public function testGetPayment()
	{
		$apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', []);
		$context = new PayPalContext($apiContext);
		$operation = new DummyPaymentOperation($context);

		$payment = $operation->getPayment();
		Assert::equal('123456789', $payment->transactions[0]->invoice_number);
	}

	public function testCreatePayment()
	{
		$credentials = new OAuthTokenCredential($this->config['clientId'], $this->config['secretId']);
		$apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', [$credentials])->makePartial();
		$context = new PayPalContext($apiContext);
		$operation = new DummyPaymentOperation($context);
		$payment = $operation->getPayment();

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl('http://localhost/return');
		$redirectUrls->setCancelUrl('http://localhost/cancel');
		$payment->setRedirectUrls($redirectUrls);

		$result = $operation->createPayment($payment);
		Assert::equal('created', $result->getState());
		Assert::equal('http://localhost/return', $payment->getRedirectUrls()->getReturnUrl());
		Assert::notEqual(null, $result->getApprovalLink());
	}

	public function testCreatePaymentGaTracking()
	{
		$credentials = new OAuthTokenCredential($this->config['clientId'], $this->config['secretId']);
		$apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', [$credentials])->makePartial();
		$context = new PayPalContext($apiContext);
		$context->setGaTrackingEnabled(true);

		$operation = new DummyPaymentOperation($context);
		$payment = $operation->getPayment();

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl('http://localhost/return');
		$redirectUrls->setCancelUrl('http://localhost/cancel');
		$payment->setRedirectUrls($redirectUrls);

		$result = $operation->createPayment($payment);
		Assert::equal('created', $result->getState());
		Assert::equal('http://localhost/return?utm_nooverride=1', $payment->getRedirectUrls()->getReturnUrl());
		Assert::notEqual(null, $result->getApprovalLink());
	}

	public function testCreatePaymentSimpleOperation()
	{
		$credentials = new OAuthTokenCredential($this->config['clientId'], $this->config['secretId']);
		$apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', [$credentials])->makePartial();
		$context = new PayPalContext($apiContext);
		$context->setCurrency('CZK');
		$operation = new SimplePaymentOperation($context, "Coffee", 10);
		$payment = $operation->getPayment();

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl('http://localhost/return');
		$redirectUrls->setCancelUrl('http://localhost/cancel');
		$payment->setRedirectUrls($redirectUrls);

		$result = $operation->createPayment($payment);
		Assert::equal('created', $result->getState());
		Assert::notEqual(null, $result->getApprovalLink());
	}

	public function testCreatePaymentPlainPaymentOperation()
	{
		$credentials = new OAuthTokenCredential($this->config['clientId'], $this->config['secretId']);
		$apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', [$credentials])->makePartial();
		$context = new PayPalContext($apiContext);

		$item = TransactionHelper::createItem('Coffee', 'EUR', 2, '#123', 20);
		$itemList = TransactionHelper::createItemList([$item]);
		$details = TransactionHelper::createDetails(1, 2, 40);
		$amount = TransactionHelper::createAmount($details, 43, 'EUR');
		$invoiceNumber = '123456789';
		$description = 'The best coffee ever';
		$transaction = TransactionHelper::createTransaction($amount, $itemList, $invoiceNumber, $description);

		$plainOperation = new PlainPaymentOperation($context, [$transaction]);
		/** @var Payment $payment */
		$payment = $plainOperation->getPayment();

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl('http://localhost/return');
		$redirectUrls->setCancelUrl('http://localhost/cancel');
		$payment->setRedirectUrls($redirectUrls);

		/** @var Payment $result */
		$result = $plainOperation->createPayment($payment);
		Assert::equal('created', $result->getState());
		Assert::notEqual(null, $result->getApprovalLink());
	}

	/**
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		parent::tearDown();
		\Mockery::close();
	}

}

\run(new PaymentOperationTest());


