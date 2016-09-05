<?php

namespace MetisFW\PayPal\Notification;

interface BasicNotificationOperationFactory {

  /**
   * @return BasicNotificationOperation
   */
  public function create();

}
