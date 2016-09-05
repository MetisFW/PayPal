<?php

namespace MetisFW\PayPal\Notification;

use Nette\Http\Request;
use Nette\Http\Response;

interface NotificationOperation {

  /**
   * @param Request $request
   * @return Response
   */
  public function handleNotification(Request $request);

}
