<?php

namespace MetisFW\PayPal\Payment;

interface SimplePaymentOperationFactory {

  /**
   * @param string $name
   * @param int|float $price
   * @param int $quantity
   * @param string|null $currency
   *
   * @return SimplePaymentOperation
   */
  public function create(string $name, $price, int $quantity = 1, string $currency = null) : SimplePaymentOperation;

}
