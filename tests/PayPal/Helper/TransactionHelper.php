<?php declare(strict_types=1);

namespace Tests\PayPal\Helper;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;

class TransactionHelper
{

	static public function createTransaction(Amount $amount, ItemList $itemLists, string $invoiceNumber, string $description): Transaction
	{
		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setItemList($itemLists);
		$transaction->setInvoiceNumber($invoiceNumber);
		$transaction->setDescription($description);
		return $transaction;
	}

	static public function createAmount(Details $details, int $total, string $currency): Amount
	{
		$amount = new Amount();
		$amount->setCurrency($currency);
		$amount->setTotal($total);
		$amount->setDetails($details);
		return $amount;
	}

	static public function createDetails(float $shippingPrice, float $taxPrice, float $subtotal): Details
	{
		$details = new Details();
		$details->setShipping($shippingPrice);
		$details->setTax($taxPrice);

		$details->setSubtotal($subtotal);
		return $details;
	}

	static public function createItemList(array $items): ItemList
	{
		$itemList = new ItemList();
		$itemList->setItems($items);
		return $itemList;
	}

	static public function createItem(string $name, string $currency, int $quantity, string $sku, int $price): Item
	{
		$item = new Item();
		$item->setName($name);
		$item->setCurrency($currency);
		$item->setQuantity((string) $quantity);
		$item->setSku($sku);
		$item->setPrice($price);
		return $item;
	}

}
