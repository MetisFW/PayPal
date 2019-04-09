<?php declare(strict_types=1);

namespace Tests\PayPal\Payment;

use MetisFW\PayPal\Payment\BasePaymentOperation;
use Tests\PayPal\Helper\TransactionHelper;

require_once __DIR__ . '/../Helper/TransactionHelper.php';

class DummyPaymentOperation extends BasePaymentOperation
{

	/**
	 * @return array array of PayPal\Api\Transaction
	 */
	protected function getTransactions(): array
	{
		$item = TransactionHelper::createItem('Coffee', 'EUR', 2, '#123', 20);
		$itemList = TransactionHelper::createItemList([$item]);
		$details = TransactionHelper::createDetails(1, 2, 40);
		$amount = TransactionHelper::createAmount($details, 43, 'EUR');
		$invoiceNumber = '123456789';
		$description = 'The best coffee ever';
		$transaction = TransactionHelper::createTransaction($amount, $itemList, $invoiceNumber, $description);
		return [$transaction];
	}

}
