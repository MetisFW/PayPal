<?php declare(strict_types=1);

namespace MetisFW\PayPal\Payment;

use PayPal\Api\Payment;

interface PaymentOperation
{

	/**
	 * Create paypal payment
	 */
	public function getPayment(): Payment;

	/**
	 * Execute payment api call
	 */
	public function createPayment(Payment $payment): Payment;

	public function handleReturn(string $paymentId, string $payerId): Payment;

	public function handleCancel(): void;

}