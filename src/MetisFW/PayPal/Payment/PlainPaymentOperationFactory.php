<?php

namespace MetisFW\PayPal\Payment;

interface PlainPaymentOperationFactory
{

  /**
   * @param array $transactions
   *
   * @return PlainPaymentOperation
   */
  public function create(array $transactions);
}
