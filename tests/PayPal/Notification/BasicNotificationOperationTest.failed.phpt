<?php

namespace MetisFWTests\PayPal\Notification;

use MetisFW\PayPal\Notification\BasicNotificationOperation;
use Mockery\MockInterface;
use Nette\Http\Request;
use PayPal\Api\WebhookEvent;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__.'/../../bootstrap.php';

class BasicNotificationOperationFailedTest extends TestCase {

  /** @var MockInterface */
  private $apiContext;

  /** @var BasicNotificationOperation */
  private $operation;

  /** @var int */
  public $onNotificationCounter;

  /** @var int */
  public $onSuccessCounter;

  /** @var int */
  public $onFailedCounter;

  /** @var int */
  public $onPaymentSaleCompletedCounter;

  /** @var int */
  public $onPaymentSaleDeniedCounter;

  /** @var int */
  public $onPaymentSalePendingCounter;

  protected function setUp() {
    parent::setUp();

    $this->apiContext = $apiContext = \Mockery::mock('\PayPal\Rest\ApiContext', array());
    $this->operation = new BasicNotificationOperation($apiContext);
    $self = $this;

    $this->onNotificationCounter = 0;
    $this->operation->onNotification[] = function (BasicNotificationOperation $sender, Request $request) use ($self) {
      $self->onNotificationCounter++;
    };

    $this->onSuccessCounter = 0;
    $this->operation->onSuccess[] = function (BasicNotificationOperation $sender, WebhookEvent $event) use ($self) {
      $self->onSuccessCounter++;
    };

    $this->onFailedCounter = 0;
    $this->operation->onFailed[] = function (BasicNotificationOperation $sender, Request $request) use ($self) {
      $self->onFailedCounter++;
    };

    $this->onPaymentSaleCompletedCounter = 0;
    $this->operation->onPaymentSaleCompleted[] = function (BasicNotificationOperation $sender, WebhookEvent $event) use
    (
      $self
    ) {
      $self->onPaymentSaleCompletedCounter++;
    };

    $this->onPaymentSaleDeniedCounter = 0;
    $this->operation->onPaymentSaleDenied[] = function (BasicNotificationOperation $sender, WebhookEvent $event) use (
      $self
    ) {
      $self->onPaymentSaleDeniedCounter++;
    };

    $this->onPaymentSalePendingCounter = 0;
    $this->operation->onPaymentSalePending[] = function (BasicNotificationOperation $sender, WebhookEvent $event) use (
      $self
    ) {
      $self->onPaymentSalePendingCounter++;
    };
  }

  public function testFailed() {
    $request = \Mockery::mock('\Nette\Http\Request');
    $request->shouldReceive('getRawBody')
      ->andReturn($data = '{"id": "test-id", "event_type": "PAYMENT.SALE.COMPLETED"}');

    $webhookEventMock = \Mockery::mock('alias:\PayPal\Api\WebhookEvent');
    $webhookEventMock->shouldReceive('validateAndGetReceivedEvent')->andThrow(new \InvalidArgumentException());

    $response = $this->operation->handleNotification($request);

    Assert::equal(503, $response->getCode());
    Assert::equal(1, $this->onNotificationCounter);
    Assert::equal(0, $this->onSuccessCounter);
    Assert::equal(1, $this->onFailedCounter);
    Assert::equal(0, $this->onPaymentSaleCompletedCounter);
    Assert::equal(0, $this->onPaymentSaleDeniedCounter);
    Assert::equal(0, $this->onPaymentSalePendingCounter);
  }

}

\run(new BasicNotificationOperationFailedTest());
