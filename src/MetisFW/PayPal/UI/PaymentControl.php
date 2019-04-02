<?php

namespace MetisFW\PayPal\UI;

use MetisFW\PayPal\Payment\PaymentOperation;
use MetisFW\PayPal\PayPalException;
use Nette\Application\UI\Control;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;

class PaymentControl extends Control {

  /**
   * @var PaymentOperation
   */
  private $operation;

  /**
   * @var string
   */
  private $templateFilePath;

  /**
   * @var array of callbacks, signature: function(AccountBasedPayPalControl $control, PayPalContext $context)
   */
  public $onCheckout = array();

  /**
   * @var array of callbacks, signature: function(AccountBasedPayPalControl $control, array $response)
   */
  public $onSuccess = array();

  /**
   * @var array of callbacks, signature: function(AccountBasedPayPalControl $control)
   */
  public $onCancel = array();

  /**
   * @var array of callbacks, signature: function(AccountBasedPayPalControl $control, \Exception $exception)
   */
  public $onError = array();

  /**
   * @param PaymentOperation $operation
   */
  public function __construct(PaymentOperation $operation) {
    $this->operation = $operation;
  }

  public function handleCheckout() {
    try {
      $payment = $this->operation->getPayment();
      $this->setPaymentParameters($payment);

      $createdPayment = $this->operation->createPayment($payment);
      $this->onCheckout($this, $createdPayment);

      $approvalUrl = $createdPayment->getApprovalLink();
      $this->getPresenter()->redirectUrl($approvalUrl);
    }
    catch(PayPalException $exception) {
      $this->errorHandler($exception);
    }
  }

  public function handleReturn() {
    $paymentId = $this->getPresenter()->getParameter('paymentId');
    $payerId = $this->getPresenter()->getParameter('PayerID');

    try {
      $paidPayment = $this->operation->handleReturn($paymentId, $payerId);
    }
    catch(PayPalException $exception) {
      $this->errorHandler($exception);
      return;
    }

    $this->onSuccess($this, $paidPayment);
  }

  public function handleCancel() {
    $this->operation->handleCancel();
    $this->onCancel($this);
  }

  public function setTemplateFilePath($templateFilePath) {
    $this->templateFilePath = $templateFilePath;
  }
  public function getTemplateFilePath() {
    return $this->templateFilePath ? $this->templateFilePath : $this->getDefaultTemplateFilePath();
  }

  /**
   * @param array $attrs
   * @param string $text
   */
  public function render($attrs = array(), $text = "Pay") {
    $template = $this->template;
    $templateFilePath = $this->getTemplateFilePath();
    $template->setFile($templateFilePath);
    $template->checkoutLink = $this->link('//checkout!');
    $template->text = $text;
    $template->attrs = $attrs;
    $template->render();
  }

  /**
   * @param \Exception $exception
   *
   * @throws PayPalException
   *
   * @return void
   */
  protected function errorHandler(\Exception $exception) {
    if(!$this->onError) {
      throw $exception;
    }

    $this->onError($this, $exception);
  }

  /**
   * @param Payment $payment
   */
  protected function setPaymentParameters(Payment $payment) {
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($this->link('//return!'))->setCancelUrl($this->link('//cancel!'));
    $payment->setRedirectUrls($redirectUrls);
  }

  protected function getDefaultTemplateFilePath() {
    return __DIR__.'/templates/PaymentControl.latte';
  }

}
