<?php declare(strict_types=1);

namespace MetisFW\PayPal\Payment;

interface PlainPaymentOperationFactory
{

	public function create(array $transactions): PlainPaymentOperation;

}
