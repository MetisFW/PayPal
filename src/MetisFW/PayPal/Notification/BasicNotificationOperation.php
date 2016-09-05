<?php

namespace MetisFW\PayPal\Notification;

use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Object;
use PayPal\Rest\ApiContext;

class BasicNotificationOperation extends Object implements NotificationOperation {

  /** @var ApiContext */
  private $context;

  /** @var array of callbacks, signatureL function(BasicNotificationOperation $sender, Request $request) */
  public $onNotification;

  /** @var array of callbacks, signature: function(BasicNotificationOperation $sender, WebhookEvent $event) */
  public $onSuccess;

  /** @var array of callbacks, signature: function(BasicNotificationOperation $sender, Request $request) */
  public $onFailed;

  /** @var array of callbacks, signature: function(BasicNotificationOperation $sender, WebhookEvent $event) */
  public $onPaymentSaleCompleted;

  /** @var array of callbacks, signature: function(BasicNotificationOperation $sender, WebhookEvent $event) */
  public $onPaymentSaleDenied;

  /** @var array of callbacks, signature: function(BasicNotificationOperation $sender, WebhookEvent $event) */
  public $onPaymentSalePending;

  /**
   * @param ApiContext $context
   */
  public function __construct(ApiContext $context) {
    $this->context = $context;
  }

  /**
   * @param Request $request
   */
  public function handleNotification(Request $request) {
    $this->onNotification($this, $request);
    $body = $request->getRawBody();
    $response = new Response();

    try {
      $validatedEvent = \PayPal\Api\WebhookEvent::validateAndGetReceivedEvent($body, $this->context);

      $this->onSuccess($this, $validatedEvent);
      switch($validatedEvent->getEventType()) {
        case "PAYMENT.SALE.COMPLETED":
          $this->onPaymentSaleCompleted($this, $validatedEvent);
          break;
        case "PAYMENT.SALE.DENIED":
          $this->onPaymentSaleDenied($this, $validatedEvent);
          break;
        case "PAYMENT.SALE.PENDING":
          $this->onPaymentSalePending($this, $validatedEvent);
          break;
      }

      $response->setCode(200); // notification is succesfully processed and we dont want to receive it again
    } catch(\Exception $exception) {
      $this->onFailed($this, $request);
      $response->setCode(503); // when the handling process failed, we want to receive the notification later
    }

    return $response;
  }
}
