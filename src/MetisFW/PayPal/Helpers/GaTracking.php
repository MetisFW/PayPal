<?php

namespace MetisFW\PayPal\Helpers;

use Nette\Http\Url;
use Nette\SmartObject;
use PayPal\Api\Payment;

class GaTracking {

  use SmartObject;

  private function __construct() {
    // nothing
  }

  public static function addTrackingParameters(Payment $payment) : Payment {
    $redirectUrls = $payment->getRedirectUrls();

    $url = new Url($redirectUrls->getReturnUrl());
    $url->setQueryParameter('utm_nooverride', 1);

    $redirectUrls->setReturnUrl($url->getAbsoluteUrl());
    $payment->setRedirectUrls($redirectUrls);

    return $payment;
  }

}
