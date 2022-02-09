<?php declare(strict_types=1);

namespace MetisFW\PayPal\Helpers;

use Nette\Http\Url;
use PayPal\Api\Payment;

class GaTracking
{

	private function __construct()
	{
		// nothing
	}

	public static function addTrackingParameters(Payment $payment): Payment
	{
		$redirectUrls = $payment->getRedirectUrls();

		$url = new Url($redirectUrls->getReturnUrl());
		$url->setQueryParameter('utm_nooverride', 1);

		$redirectUrls->setReturnUrl($url->getAbsoluteUrl());
		$payment->setRedirectUrls($redirectUrls);

		return $payment;
	}

}
