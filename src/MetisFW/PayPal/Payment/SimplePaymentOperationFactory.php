<?php declare(strict_types=1);

namespace MetisFW\PayPal\Payment;

interface SimplePaymentOperationFactory
{

	/**
	 * @param int|float $price
	 */
	public function create(string $name, $price, int $quantity = 1, ?string $currency = null): SimplePaymentOperation;

}
