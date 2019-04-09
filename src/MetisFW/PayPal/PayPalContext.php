<?php declare(strict_types=1);

namespace MetisFW\PayPal;

use PayPal\Rest\ApiContext;

class PayPalContext
{

	/** @var ApiContext */
	private $apiContext;

	/** @var string */
	private $currency;

	/** @var bool */
	private $gaTrackingEnabled = false;

	/** @var string|null */
	private $experienceProfileId;

	public function __construct(ApiContext $apiContext)
	{
		$this->apiContext = $apiContext;
	}

	public function setConfig(array $config): void
	{
		$this->apiContext->setConfig($config);
	}

	public function setCurrency(string $currency): void
	{
		$this->currency = $currency;
	}

	public function getCurrency(): string
	{
		return $this->currency;
	}

	public function getApiContext(): ApiContext
	{
		return $this->apiContext;
	}

	public function setGaTrackingEnabled(bool $value): void
	{
		$this->gaTrackingEnabled = $value;
	}

	public function isGaTrackingEnabled(): bool
	{
		return $this->gaTrackingEnabled;
	}

	public function getExperienceProfileId(): ?string
	{
		return $this->experienceProfileId;
	}

	public function setExperienceProfileId(string $experienceProfileId): void
	{
		$this->experienceProfileId = $experienceProfileId;
	}

}
