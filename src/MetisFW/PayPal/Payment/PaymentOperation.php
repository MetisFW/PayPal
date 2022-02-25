<?php

namespace MetisFW\PayPal\Payment;

use PayPal\Api\Payment;

interface PaymentOperation {

  /**
   * Create paypal payment
   */
  public function getPayment() : Payment;

  /**
   * Execute payment api call
   */
  public function createPayment(Payment $payment) : Payment;

  /**
   * @param string $paymentId
   * @param string $payerId
   *
   * @return Payment
   */
  public function handleReturn(string $paymentId, string $payerId) : Payment;

  /**
   * @return void
   */
  public function handleCancel() : void;

}