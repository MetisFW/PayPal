<?php

namespace MetisFW\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;

class PlainPaymentOperation extends BasePaymentOperation
{

  /** @var array */
  private $transactions;

  /**
   * @param PayPalContext $context
   * @param array $transactions
   */
  public function __construct(PayPalContext $context, array $transactions)
  {
    parent::__construct($context);
    $this->checkTransactions($transactions);
    $this->transactions = $transactions;
  }

  /**
   * @return array array of PayPal\Api\Transaction
   */
  protected function getTransactions()
  {
    return $this->transactions;
  }
}
