<?php declare(strict_types=1);

namespace Tests\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/DummyPaymentOperation.php';

class PaymentOperationHandleReturnTest extends TestCase
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

	public function testHandleReturn()
	{
		$credentials = new OAuthTokenCredential($this->config['clientId'], $this->config['secretId']);
		$apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', [$credentials])->makePartial();

		$context = new PayPalContext($apiContext);
		$operation = new DummyPaymentOperation(
			$context);

		$paymentId = "123456";
		$payerId = "john.doe";

		$paymentMock = \Mockery::mock('alias:\PayPal\Api\Payment');
		$approvedPayment = $paymentMock;
		$paymentMock->shouldReceive('get')->with($paymentId, $apiContext)->andReturn($approvedPayment);
		$approvedPayment->shouldReceive('execute')->with(
			\Mockery::on(function (PaymentExecution $actualExecution) use ($payerId) {
				return $actualExecution->getPayerId() == $payerId;
			}),
			\Mockery::on(function (ApiContext $actualApiContext) use ($apiContext) {
				return $actualApiContext === $apiContext;
			})
		);
		$result = new Payment();
		$paymentMock->shouldReceive('get')->with($paymentId, $payerId)->andReturn($result);

		$payment = $operation->handleReturn($paymentId, $payerId);

		Assert::true($payment === $approvedPayment);
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

\run(new PaymentOperationHandleReturnTest());
