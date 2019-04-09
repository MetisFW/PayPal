<?php declare(strict_types=1);

namespace MetisFW\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;

class SimplePaymentOperation extends BasePaymentOperation
{

	/** @var string */
	private $name;

	/** @var int */
	private $quantity;

	/** @var int|float|string */
	private $price;

	/** @var string|null */
	private $currency;

	/**
	 * @param int|float|string $price
	 */
	public function __construct(PayPalContext $context, string $name, $price, int $quantity = 1, ?string $currency = null)
	{
		parent::__construct($context);
		$this->name = $name;
		$this->quantity = $quantity;
		$this->price = $price;
		$this->currency = $currency;
	}

	/**
	 * @return Transaction[]
	 */
	protected function getTransactions(): array
	{
		$payPalItems = array();
		$currency = $this->currency ? $this->currency : $this->context->getCurrency();

		$payPalItem = new Item();
		$payPalItem->setName($this->name);
		$payPalItem->setCurrency($currency);
		$payPalItem->setQuantity((string) $this->quantity);
		$payPalItem->setPrice((double) $this->price);

		$payPalItems[] = $payPalItem;
		$totalPrice = $this->quantity * (double) $this->price;

		$itemLists = new ItemList();
		$itemLists->setItems($payPalItems);

		$amount = new Amount();
		$amount->setCurrency($currency);
		$amount->setTotal($totalPrice);

		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setItemList($itemLists);

		return [$transaction];
	}

}
