<?php

namespace MetisFW\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;

class SimplePaymentOperation extends BasePaymentOperation {

  /** @var string */
  private $name;

  /** @var int */
  private $quantity;

  /** @var int|float|string */
  private $price;

  /** @var string|null */
  private $currency;

  /**
   * @param PayPalContext $context
   * @param string $name
   * @param int|float|string $price
   * @param int $quantity
   * @param string|null $currency
   */
  public function __construct(PayPalContext $context, $name, $price, $quantity = 1, $currency = null) {
    parent::__construct($context);
    $this->name = $name;
    $this->quantity = $quantity;
    $this->price = $price;
    $this->currency = $currency;
  }

  /**
   * @return array array of PayPal\Api\Transaction
   */
  protected function getTransactions() {
    $payPalItems = array();
    $currency = $this->currency ? $this->currency : $this->context->getCurrency();

    $payPalItem = new Item();
    $payPalItem->setName($this->name);
    $payPalItem->setCurrency($currency);
    $payPalItem->setQuantity($this->quantity);
    $payPalItem->setPrice($this->price);

    $payPalItems[] = $payPalItem;
    $totalPrice = $this->quantity * $this->price;

    $itemLists = new ItemList();
    $itemLists->setItems($payPalItems);

    $amount = new Amount();
    $amount->setCurrency($currency);
    $amount->setTotal($totalPrice);

    $transaction = new Transaction();
    $transaction->setAmount($amount);
    $transaction->setItemList($itemLists);

    return array($transaction);
  }

}