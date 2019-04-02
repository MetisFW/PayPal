<?php

namespace MetisFW\PayPal;

use PayPal\Rest\ApiContext;

class PayPalContext {

  /** @var ApiContext */
  private $apiContext;

  /** @var string */
  private $currency;

  /** @var bool */
  private $gaTrackingEnabled;

  /** @var string */
  private $experienceProfileId;

  /**
   * @param string $clientId
   * @param string $secret
   */
  public function __construct(ApiContext $apiContext) {
    $this->apiContext = $apiContext;
  }

  /**
   * @param array $config
   */
  public function setConfig(array $config) {
    $this->apiContext->setConfig($config);
  }

  /**
   * @param string $currency
   */
  public function setCurrency($currency) {
    $this->currency = $currency;
  }

  /**
   * @return string
   */
  public function getCurrency() {
    return $this->currency;
  }

  /**
   * @return ApiContext
   */
  public function getApiContext() {
    return $this->apiContext;
  }

  /**
   * @param bool $value
   */
  public function setGaTrackingEnabled($value) {
    $this->gaTrackingEnabled = $value;
  }

  /**
   * @return bool
   */
  public function isGaTrackingEnabled() {
    return $this->gaTrackingEnabled;
  }

  /**
   * @return string
   */
  public function getExperienceProfileId() {
    return $this->experienceProfileId;
  }

  /**
   * @param string $experienceProfileId
   */
  public function setExperienceProfileId($experienceProfileId) {
    $this->experienceProfileId = $experienceProfileId;
  }

}
