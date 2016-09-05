# MetisFW/PayPal

## Setup

1) Register extension
```
extensions:
  payPal: MetisFW\PayPal\DI\PayPalExtension
```

2) Set up extension parameters

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

This library allows to:
1) Add PayPal gateway (see [link](https://github.com/MetisFW/PayPal/blob/master/docs/en/payment.md))
2) Receive notifications via webhooks (see [link](https://github.com/MetisFW/PayPal/blob/master/docs/en/notification.md)) 
