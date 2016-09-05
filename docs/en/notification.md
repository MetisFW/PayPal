## How to receive notifications

Prerequisites:
1. Setup webhooks in your PayPal application administration
2. Create a presenter/control that handles paypal notifications (webhook url have to point to the action where notifications are processed)

##### Sample usage

###### In Presenter
```php
use \MetisFW\Paypal\Notification\BasicNotificationOperation;
use \Nette\Application\UI\Presenter;
use \PayPal\Api\WebhookEvent;

class PaymentPresenter extends Presenter {

  /** @var BasicNotificationOperationFactory @inject */
  private $payPalNotificationOperation;

  public function actionPayPalNotification() {
    $request = $this->getRequest();
    $operation = $payPalNotificationOperationFactory->create();
    
    // called when notification occured (can be valid or faked)
    $operation->onNotification[] = function ($sender, Request $request) {
      // something
    }
    
    // called when notification occured and it is valid
    $operation->onSuccess[] = function ($sender, WebhookEvent $event) {
      // something
    }
    
    // called when valid notification occured and its type is PAYMENT.SALE.COMPLETED
    $operation->onPaymentSaleCompleted[] = function ($sender, WebhookEvent $event) {
      // something
    }
    
    // called when valid notification occured and its type is PAYMENT.SALE.DENIED
    $operation->onPaymentSaleDenied[] = function ($sender, WebhookEvent $event) {
      // something
    }
    
    // called when valid notification occured and its type is PAYMENT.SALE.PENDING
    $operation->onPaymentSalePending[] = function ($sender, WebhookEvent $event) {
       // something
     }
    
    // called when notification validation failed
    $operation->onFailed[] = function ($sender, Request $request) {
      // something
    }


    
    $response = $operation->handleNotification($request);
    $this->sendResponse($response);
  }
}
```
