<?php

namespace PayPal\Payment;

use MetisFW\PayPal\Helper\TransactionHelper;
use MetisFW\PayPal\Payment\BasePaymentOperation;
use PayPal\Api\Transaction;

require '../Helper/TransactionHelper.php';

class DummyPaymentOperation extends BasePaymentOperation {

  /**
   * @return Transaction[]
   */
  protected function getTransactions() : array {
    $item = TransactionHelper::createItem('Coffee', 'EUR', 2, '#123', 20);
    $itemList = TransactionHelper::createItemList(array($item));
    $details = TransactionHelper::createDetails(1, 2, 40);
    $amount = TransactionHelper::createAmount($details, 43, 'EUR');
    $invoiceNumber = '123456789';
    $description = 'The best coffee ever';
    $transaction = TransactionHelper::createTransaction($amount, $itemList, $invoiceNumber, $description);
    return array($transaction);
  }

}