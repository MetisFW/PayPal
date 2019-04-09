<?php declare(strict_types=1);

namespace MetisFW\PayPal\UI;

use MetisFW\PayPal\Payment\PaymentOperation;
use MetisFW\PayPal\PayPalContext;
use MetisFW\PayPal\PayPalException;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;

/**
 * @method void onCancel(PaymentControl $control)
 * @method void onError(PaymentControl $control, \Exception $exception)
 * @method void onCheckout(PaymentControl $control, PayPalContext $context)
 * @method void onSuccess(PaymentControl $control, array $response)
 * @property-read Template $template
 */
class PaymentControl extends Control
{

	/**
	 * @var PaymentOperation
	 */
	private $operation;

	/**
	 * @var string
	 */
	private $templateFilePath;

	/**
	 * @var array of callbacks, signature: function(PaymentControl $control, PayPalContext $context)
	 */
	public $onCheckout = [];

	/**
	 * @var array of callbacks, signature: function(PaymentControl $control, array $response)
	 */
	public $onSuccess = [];

	/**
	 * @var array of callbacks, signature: function(PaymentControl $control)
	 */
	public $onCancel = [];

	/**
	 * @var array of callbacks, signature: function(PaymentControl $control, \Exception $exception)
	 */
	public $onError = [];

	public function __construct(PaymentOperation $operation)
	{
		$this->operation = $operation;
	}

	public function handleCheckout(): void
	{
		try {
			$payment = $this->operation->getPayment();
			$this->setPaymentParameters($payment);

			$createdPayment = $this->operation->createPayment($payment);
			$this->onCheckout($this, $createdPayment);

			$approvalUrl = $createdPayment->getApprovalLink();
			$this->getPresenter()->redirectUrl($approvalUrl);
		} catch (PayPalException $exception) {
			$this->errorHandler($exception);
		}
	}

	public function handleReturn(?string $paymentId, ?string $payerId): void
	{
		$paymentId = $paymentId ?: $this->getPresenter()->getParameter('paymentId');
		$payerId = $payerId ?: $this->getPresenter()->getParameter('PayerID');

		try {
			$paidPayment = $this->operation->handleReturn($paymentId, $payerId);
		} catch (PayPalException $exception) {
			$this->errorHandler($exception);
			return;
		}

		$this->onSuccess($this, $paidPayment);
	}

	public function handleCancel(): void
	{
		$this->operation->handleCancel();
		$this->onCancel($this);
	}

	public function setTemplateFilePath(string $templateFilePath): void
	{
		$this->templateFilePath = $templateFilePath;
	}

	public function getTemplateFilePath(): string
	{
		return $this->templateFilePath ? $this->templateFilePath : $this->getDefaultTemplateFilePath();
	}

	public function render(array $attrs = [], string $text = "Pay"): void
	{
		$template = $this->template;
		$templateFilePath = $this->getTemplateFilePath();
		$template->setFile($templateFilePath);
		$template->checkoutLink = $this->link('//checkout!');
		$template->text = $text;
		$template->attrs = $attrs;
		$template->render();
	}

	/**
	 * @throws PayPalException
	 */
	protected function errorHandler(\Exception $exception): void
	{
		if (!$this->onError) {
			throw $exception;
		}

		$this->onError($this, $exception);
	}

	protected function setPaymentParameters(Payment $payment): void
	{
		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($this->link('//return!'))->setCancelUrl($this->link('//cancel!'));
		$payment->setRedirectUrls($redirectUrls);
	}

	protected function getDefaultTemplateFilePath(): string
	{
		return __DIR__ . '/templates/PaymentControl.latte';
	}

}
