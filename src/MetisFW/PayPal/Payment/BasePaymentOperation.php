<?php declare(strict_types=1);

namespace MetisFW\PayPal\Payment;

use MetisFW\PayPal\Helpers\GaTracking;
use MetisFW\PayPal\PayPalContext;
use MetisFW\PayPal\PayPalException;
use Nette\SmartObject;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;

/**
 * @method void onCancel(self $paymentOperation)
 * @method void onReturn(self $paymentOperation, Payment $payment)
 */
abstract class BasePaymentOperation implements PaymentOperation
{

	use SmartObject;

	/** @var PayPalContext */
	protected $context;

	/**
	 * @var array array of callbacks, signature: function ($this) {...}
	 */
	public $onCancel;

	/**
	 * @var array array of callbacks, signature: function ($this, Payment $payment) {...}
	 */
	public $onReturn;

	public function __construct(PayPalContext $context)
	{
		$this->context = $context;
	}

	/**
	 * @return Transaction[]
	 */
	abstract protected function getTransactions(): array;

	protected function getPayer(): Payer
	{
		return new Payer();
	}

	/**
	 * @see http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/CreatePaymentUsingPayPal.html
	 */
	public function getPayment(): Payment
	{
		$payer = $this->getPayer();

		if (!$payer->getPaymentMethod()) {
			$payer->setPaymentMethod('paypal');
		}

		$payment = new Payment();
		$payment->setIntent("sale")
			->setPayer($payer);

		if ($this->context->getExperienceProfileId()) {
			$payment->setExperienceProfileId($this->context->getExperienceProfileId());
		}

		$transactions = $this->getTransactions();
		$this->checkTransactions($transactions);
		$payment->setTransactions($transactions);

		return $payment;
	}

	/**
	 * Execute payment api call
	 */
	public function createPayment(Payment $payment): Payment
	{
		if ($this->context->isGaTrackingEnabled()) {
			$payment = GaTracking::addTrackingParameters($payment);
		}

		try {
			return $payment->create($this->context->getApiContext());
		} catch (\Exception $exception) {
			throw $this->translateException($exception);
		}
	}

	public function handleReturn(string $paymentId, string $payerId): Payment
	{
		try {
			$payment = Payment::get($paymentId, $this->context->getApiContext());
			$execution = new PaymentExecution();
			$execution->setPayerId($payerId);

			$payment->execute($execution, $this->context->getApiContext());
			$paidPayment = Payment::get($paymentId, $this->context->getApiContext());
		} catch (\Exception $exception) {
			throw $this->translateException($exception);
		}

		$this->onReturn($this, $paidPayment);

		return $paidPayment;
	}

	public function handleCancel(): void
	{
		$this->onCancel($this);
	}

	protected function translateException(\Exception $exception): \Exception
	{
		if ($exception instanceof PayPalConfigurationException ||
			$exception instanceof PayPalInvalidCredentialException ||
			$exception instanceof PayPalMissingCredentialException ||
			$exception instanceof PayPalConnectionException
		) {

			$message = $exception->getMessage();

			if ($exception instanceof PayPalConnectionException) {
				$message .= ' Data: ' . $exception->getData();
			}
			return new PayPalException(
				$message,
				$exception->getCode(),
				$exception);
		}

		return $exception;
	}

	/**
	 * @throws \LogicException throws when some item from array is not instance of \PayPal\Api\Transaction
	 */
	protected function checkTransactions(array $transactions): void
	{
		foreach ($transactions as $transaction) {
			if (!$transaction instanceof Transaction) {
				throw new \LogicException('Expect array of \PayPal\Api\Transaction instances but instance of ' .
					gettype($transaction) . ' given');
			}
		}
	}

}
