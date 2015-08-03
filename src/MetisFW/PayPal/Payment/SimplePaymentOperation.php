<?php

namespace MetisFW\PayPal\Payment;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;

class SimplePaymentOperation extends BasePaymentOperation {

  /** @var array */
  private $transactions = array();

  /**
   * @return array array of PayPal\Api\Transaction
   */
  protected function getTransactions() {
    return $this->transactions;
  }

  /**
   * @param Transaction $transaction
   */
  public function addTransaction(Transaction $transaction) {
    $this->transactions[] = $transaction;
  }

  /**
   * @param Amount $amount
   * @param ItemList $itemLists
   * @param string $invoiceNumber
   * @param string $description
   *
   * @return Transaction
   */
  public function createTransaction(Amount $amount, ItemList $itemLists, $invoiceNumber, $description) {
    $transaction = new Transaction();
    $transaction->setAmount($amount);
    $transaction->setItemList($itemLists);
    $transaction->setInvoiceNumber($invoiceNumber);
    $transaction->setDescription($description);
    return $transaction;
  }

  /**
   * @param string $currency
   * @param int $total
   * @param Details $details
   *
   * @return Amount
   */
  public function createAmount(Details $details, $total, $currency) {
    $amount = new Amount();
    $amount->setCurrency($currency);
    $amount->setTotal($total);
    $amount->setDetails($details);
    return $amount;
  }

  /**
   * @param float $shippingPrice
   * @param float $taxPrice
   * @param float $subtotal
   *
   * @return Details
   */
  public function createDetails($shippingPrice, $taxPrice, $subtotal) {
    $details = new Details();
    $details->setShipping($shippingPrice);
    $details->setTax($taxPrice);

    $details->setSubtotal($subtotal);
    return $details;
  }

  /**
   * @param array $items
   *
   * @return ItemList
   */
  public function createItemList(array $items) {
    $itemList = new ItemList();
    $itemList->setItems($items);
    return $itemList;
  }

  /**
   * @param string $name
   * @param int $currency
   * @param int $quantity
   * @param string $sku
   * @param int $price
   * @return Item
   */
  public function createItem($name, $currency, $quantity, $sku, $price) {
    $item = new Item();
    $item->setName($name);
    $item->setCurrency($currency);
    $item->setQuantity($quantity);
    $item->setSku($sku);
    $item->setPrice($price);
    return $item;
  }

}