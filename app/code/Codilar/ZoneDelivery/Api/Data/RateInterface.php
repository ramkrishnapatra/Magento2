<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface RateInterface
 * Represents a shipping rate tier entity
 */
interface RateInterface extends ExtensibleDataInterface
{
    public const RATE_ID = 'rate_id';
    public const ZONE_ID = 'zone_id';
    public const WEBSITE_ID = 'website_id';
    public const WEIGHT_FROM = 'weight_from';
    public const WEIGHT_TO = 'weight_to';
    public const ORDER_VALUE_FROM = 'order_value_from';
    public const ORDER_VALUE_TO = 'order_value_to';
    public const CHARGE = 'charge';
    public const OVERSIZED_SURCHARGE = 'oversized_surcharge';

    /**
     * Get rate ID
     *
     * @return int|null
     */
    public function getRateId(): ?int;

    /**
     * Set rate ID
     *
     * @param int $rateId
     * @return $this
     */
    public function setRateId($rateId): self;

    /**
     * Get zone ID
     *
     * @return int|null
     */
    public function getZoneId(): ?int;

    /**
     * Set zone ID
     *
     * @param int $zoneId
     * @return $this
     */
    public function setZoneId(int $zoneId): self;

    /**
     * Get website ID
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int;

    /**
     * Set website ID
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId(int $websiteId): self;

    /**
     * Get weight from
     *
     * @return float|null
     */
    public function getWeightFrom(): ?float;

    /**
     * Set weight from
     *
     * @param float $weightFrom
     * @return $this
     */
    public function setWeightFrom(float $weightFrom): self;

    /**
     * Get weight to
     *
     * @return float|null
     */
    public function getWeightTo(): ?float;

    /**
     * Set weight to
     *
     * @param float|null $weightTo
     * @return $this
     */
    public function setWeightTo(?float $weightTo): self;

    /**
     * Get order value from
     *
     * @return float|null
     */
    public function getOrderValueFrom(): ?float;

    /**
     * Set order value from
     *
     * @param float $orderValueFrom
     * @return $this
     */
    public function setOrderValueFrom(float $orderValueFrom): self;

    /**
     * Get order value to
     *
     * @return float|null
     */
    public function getOrderValueTo(): ?float;

    /**
     * Set order value to
     *
     * @param float|null $orderValueTo
     * @return $this
     */
    public function setOrderValueTo(?float $orderValueTo): self;

    /**
     * Get charge
     *
     * @return float|null
     */
    public function getCharge(): ?float;

    /**
     * Set charge
     *
     * @param float $charge
     * @return $this
     */
    public function setCharge(float $charge): self;

    /**
     * Get oversized item surcharge
     *
     * @return float|null
     */
    public function getOversizedSurcharge(): ?float;

    /**
     * Set oversized item surcharge
     *
     * @param float|null $oversizedSurcharge
     * @return $this
     */
    public function setOversizedSurcharge(?float $oversizedSurcharge): self;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Codilar\ZoneDelivery\Api\Data\RateExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Codilar\ZoneDelivery\Api\Data\RateExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Codilar\ZoneDelivery\Api\Data\RateExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Codilar\ZoneDelivery\Api\Data\RateExtensionInterface $extensionAttributes
    ): self;
}
