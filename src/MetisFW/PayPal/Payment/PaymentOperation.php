<?php

namespace MetisFW\PayPal\Payment;

use PayPal\Api\Payment;

interface PaymentOperation {

  /**
   * Create paypal payment
   *
   * @return Payment
   */
  public function getPayment();

  /**
   * Execute payment api call
   *
   * @param Payment $payment
   *
   * @return Payment
   */
  public function createPayment(Payment $payment);

  /**
   * @param string $paymentId
   * @param string $payerId
   *
   * @return void
   */
  public function handleReturn($paymentId, $payerId);

  /**
   * @return void
   */
  public function handleCancel();

}