MetisFW/PayPal
======

[![Build Status](https://travis-ci.org/MetisFW/PayPal.svg?branch=master)](https://travis-ci.org/MetisFW/PayPal)
[![Downloads this Month](https://img.shields.io/packagist/dm/metisfw/paypal.svg)](https://packagist.org/packages/metisfw/paypal)
[![Latest stable](https://img.shields.io/packagist/v/metisfw/paypal.svg)](https://packagist.org/packages/metisfw/paypal)

About
------------
PayPal payment integration to Nette framework.  
Internally use [paypal/PayPal-PHP-SDK](https://github.com/paypal/PayPal-PHP-SDK) for api requests.

Inspired by [Kdyby/PayPalExpress](https://github.com/Kdyby/PayPalExpress)

Requirements
------------
MetisFW/PayPal requires PHP 5.3.2 or higher with curl, json and openssl (for lower PHP version) extensions.

- [Nette Framework](https://github.com/nette/nette)


Installation
------------
1) The best way to install MetisFW/PayPal is using  [Composer](http://getcomposer.org/):

```sh
$ composer require metisfw/paypal
```

2) Register extension
```
extensions:
  payPal: MetisFW\PayPal\DI\PayPalExtension
```

3) Set up extension parameters

```neon
payPal:
  clientId: AUqne4ywvozUaSQ1THTZYKFr88bhtA0SS_fXBoJTfeSTIasDBWuXLiLcFlfmSXRfL-kZ3Z5shvNrT6rP
  secret: EDGPDc3a65JBBY7-IKkNak7aGTVTvY-NhJgfhptegSML58fWjfp89U7UKNgGk9UI-UEZ-btfaE2sGST1
  currency: EUR
  sdkConfig:
    mode: sandbox
    log.Enabled: true
    log.FileName: '%tempDir%/PayPal.log'
    log.LogLevel: DEBUG
    validation.level: log
    cache.enabled: true
    # 'http.CURLOPT_CONNECTTIMEOUT' => 30
    # 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'/
```

sdkConfig is config to [paypal/PayPal-PHP-SDK](https://github.com/paypal/PayPal-PHP-SDK)
see [sdk-config-sample](https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/sdk_config.ini)

-----

Homepage [MetisFW](https://github.com/MetisFW) and repository [MetisFW/PayPal](https://github.com/MetisFW/PayPal).