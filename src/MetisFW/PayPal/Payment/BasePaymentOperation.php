<?php

namespace MetisFW\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;
use MetisFW\PayPal\PayPalException;
use Nette\Object;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;

abstract class BasePaymentOperation extends Object implements PaymentOperation {

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

  /**
   * @param PayPalContext $context
   */
  public function __construct(PayPalContext $context) {
    $this->context = $context;
  }

  /**
   * @return array array of PayPal\Api\Transaction
   */
  abstract protected function getTransactions();

  /**
   * @see http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/CreatePaymentUsingPayPal.html
   *
   * @return Payment
   */
  public function getPayment() {
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');

    $payment = new Payment();
    $payment->setIntent("sale")
      ->setPayer($payer);

    $transactions = $this->getTransactions();
    $this->checkTransactions($transactions);
    $payment->setTransactions($transactions);

    return $payment;
  }

  /**
   * Execute payment api call
   *
   * @return Payment
   */
  public function createPayment(Payment $payment) {
    try {
      return $payment->create($this->context->getApiContext());
    }
    catch(\Exception $exception) {
      throw $this->translateException($exception);
    }
  }

  /**
   * @param string $paymentId
   * @param string $payerId
   *
   * @return void
   */
  public function handleReturn($paymentId, $payerId) {
    try {
      $payment = Payment::get($paymentId, $this->context->getApiContext());
      $execution = new PaymentExecution();
      $execution->setPayerId($payerId);

      $payment->execute($execution, $this->context->getApiContext());
      $paidPayment = Payment::get($paymentId, $this->context->getApiContext());
    }
    catch(\Exception $exception) {
      throw $this->translateException($exception);
    }

    $this->onReturn($this, $paidPayment);
    return $paidPayment;
  }

  /**
   * @return void
   */
  public function handleCancel() {
    $this->onCancel($this);
  }

  /**
   * @param \Exception $exception
   * @return \Exception
   */
  protected function translateException(\Exception $exception) {
    if($exception instanceof PayPalConfigurationException ||
      $exception instanceof PayPalInvalidCredentialException ||
      $exception instanceof PayPalMissingCredentialException ||
      $exception instanceof PayPalConnectionException
    ) {
      return new PayPalException($exception->getMessage(), $exception->getCode(), $exception);
    }

    return $exception;
  }

  /**
   * @param array $transactions
   *
   * @throws \LogicException throws when some item from array is not instance of \PayPal\Api\Transaction
   */
  protected function checkTransactions(array $transactions) {
    foreach($transactions as $transaction) {
      if(!$transaction instanceof Transaction) {
        throw new \LogicException('Expect array of \PayPal\Api\Transaction instances but instance of '.
          gettype($transaction).' given');
      }
    }
  }

}
