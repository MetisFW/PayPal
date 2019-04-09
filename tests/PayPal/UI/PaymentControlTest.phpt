<?php declare(strict_types=1);

namespace PayPal\UI\Payment;

use MetisFW\PayPal\UI\PaymentControl;
use Mockery\MockInterface;
use PayPal\Api\Payment;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class PaymentControlTest extends TestCase
{

	/**
	 * @var MockInterface
	 */
	private $operationMock;

	/**
	 * @var PaymentControl
	 */
	private $control;

	public function setUp()
	{
		parent::setUp();

		$this->operationMock = \Mockery::mock('\MetisFW\PayPal\Payment\PaymentOperation');
		$this->control = \Mockery::mock('\MetisFW\PayPal\UI\PaymentControl', [$this->operationMock])
			->makePartial()
			->shouldAllowMockingProtectedMethods();
	}

	public function testCheckout()
	{
		$plainPayment = new Payment();
		$this->operationMock->shouldReceive('getPayment')->andReturn($plainPayment);

		$createdPayment = \Mockery::mock('\PayPal\Api\Payment');
		$this->operationMock->shouldReceive('createPayment')->with($plainPayment)->andReturn($createdPayment);
		$approvalLink = "www.example.com";
		$createdPayment->shouldReceive('getApprovalLink')->andReturn($approvalLink);

		$this->control->shouldReceive('setPaymentParameters')->once();
		$this->control->shouldReceive('onCheckout');
		$this->control->shouldReceive('getPresenter->redirectUrl')->with($approvalLink);
		$this->control->handleCheckout();

		Assert::true(true);
	}

	public function testHandleReturn()
	{
		$paymentId = "123456";
		$payerId = "654321";
		$this->control->shouldReceive('getPresenter->getParameter')->with('paymentId')->andReturn($paymentId);
		$this->control->shouldReceive('getPresenter->getParameter')->with('PayerID')->andReturn($payerId);

		$this->operationMock->shouldReceive('handleReturn');
		$this->control->shouldReceive('onSuccess');
		$this->control->handleReturn($paymentId, $payerId);
	}

	public function testHandleCancel()
	{
		$this->operationMock->shouldReceive('handleCancel');
		$this->control->shouldReceive('onCancel');
		$this->control->handleCancel();
	}

	public function testSetup()
	{
		Assert::same($this->control->getTemplateFilePath(), $this->control->getDefaultTemplateFilePath());
		$templateFilePath = '/foo/path.latte';
		$this->control->setTemplateFilePath($templateFilePath);
		Assert::same($this->control->getTemplateFilePath(), $templateFilePath);
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

run(new PaymentControlTest());

