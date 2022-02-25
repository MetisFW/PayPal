<?php

namespace MetisFW\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;
use PayPal\Api\Transaction;

class PlainPaymentOperation extends BasePaymentOperation {

  /** @var Transaction[] */
  private $transactions;

  /**
   * @param PayPalContext $context
   * @param Transaction[] $transactions
   */
  public function __construct(PayPalContext $context, array $transactions) {
    parent::__construct($context);
    $this->checkTransactions($transactions);
    $this->transactions = $transactions;
  }

  /**
   * @return Transaction[]
   */
  protected function getTransactions() : array {
    return $this->transactions;
  }

}
