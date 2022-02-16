<?php

namespace MetisFW\PayPal\Helper;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;

class TransactionHelper
{

  /**
   * @param Amount $amount
   * @param ItemList $itemLists
   * @param string $invoiceNumber
   * @param string $description
   *
   * @return Transaction
   */
  static public function createTransaction(Amount $amount, ItemList $itemLists, $invoiceNumber, $description)
  {
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
  static public function createAmount(Details $details, $total, $currency)
  {
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
  static public function createDetails($shippingPrice, $taxPrice, $subtotal)
  {
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
  static public function createItemList(array $items)
  {
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
  static public function createItem($name, $currency, $quantity, $sku, $price)
  {
    $item = new Item();
    $item->setName($name);
    $item->setCurrency($currency);
    $item->setQuantity($quantity);
    $item->setSku($sku);
    $item->setPrice($price);
    return $item;
  }
}
