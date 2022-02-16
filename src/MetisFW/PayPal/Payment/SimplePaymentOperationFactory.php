<?php

namespace MetisFW\PayPal\Payment;

interface SimplePaymentOperationFactory
{

  /**
   * @param string $name
   * @param int|float $price
   * @param int $quantity
   * @param string|null $currency
   *
   * @return SimplePaymentOperation
   */
  public function create($name, $price, $quantity = 1, $currency = null);
}
