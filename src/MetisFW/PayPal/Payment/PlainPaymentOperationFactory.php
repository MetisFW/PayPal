<?php

namespace MetisFW\PayPal\Payment;

use PayPal\Api\Transaction;

interface PlainPaymentOperationFactory {

  /**
   * @param Transaction[] $transactions
   * @return PlainPaymentOperation
   */
  public function create(array $transactions) : PlainPaymentOperation;

}
