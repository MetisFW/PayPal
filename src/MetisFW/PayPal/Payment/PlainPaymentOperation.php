<?php declare(strict_types=1);

namespace MetisFW\PayPal\Payment;

use MetisFW\PayPal\PayPalContext;
use PayPal\Api\Transaction;

class PlainPaymentOperation extends BasePaymentOperation
{

	/** @var array */
	private $transactions;

	public function __construct(PayPalContext $context, array $transactions)
	{
		parent::__construct($context);
		$this->checkTransactions($transactions);
		$this->transactions = $transactions;
	}

	/**
	 * @return Transaction[]
	 */
	protected function getTransactions(): array
	{
		return $this->transactions;
	}

}
