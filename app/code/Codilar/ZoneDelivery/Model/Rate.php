<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\RateExtensionInterface;
use Codilar\ZoneDelivery\Api\Data\RateInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Rate
 * Model representing shipping rate tier entity
 */
class Rate extends AbstractExtensibleModel implements RateInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'codilar_delivery_rate';

    /**
     * Event object parameter name
     *
     * @var string
     */
    protected $_eventObject = 'rate';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\Codilar\ZoneDelivery\Model\ResourceModel\Rate::class);
    }

    /**
     * @inheritdoc
     */
    public function getRateId(): ?int
    {
        $id = $this->getData(self::RATE_ID);
        return $id !== null ? (int)$id : null;
    }

    /**
     * @inheritdoc
     */
    public function setRateId($rateId): RateInterface
    {
        return $this->setData(self::RATE_ID, $rateId !== null ? (int)$rateId : null);
    }

    /**
     * @inheritdoc
     */
    public function getZoneId(): ?int
    {
        $zoneId = $this->getData(self::ZONE_ID);
        return $zoneId !== null ? (int)$zoneId : null;
    }

    /**
     * @inheritdoc
     */
    public function setZoneId(int $zoneId): RateInterface
    {
        return $this->setData(self::ZONE_ID, $zoneId);
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId(): ?int
    {
        $websiteId = $this->getData(self::WEBSITE_ID);
        return $websiteId !== null ? (int)$websiteId : null;
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId(int $websiteId): RateInterface
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function getWeightFrom(): ?float
    {
        $weightFrom = $this->getData(self::WEIGHT_FROM);
        return $weightFrom !== null ? (float)$weightFrom : null;
    }

    /**
     * @inheritdoc
     */
    public function setWeightFrom(float $weightFrom): RateInterface
    {
        return $this->setData(self::WEIGHT_FROM, $weightFrom);
    }

    /**
     * @inheritdoc
     */
    public function getWeightTo(): ?float
    {
        $weightTo = $this->getData(self::WEIGHT_TO);
        return $weightTo !== null ? (float)$weightTo : null;
    }

    /**
     * @inheritdoc
     */
    public function setWeightTo(?float $weightTo): RateInterface
    {
        return $this->setData(self::WEIGHT_TO, $weightTo);
    }

    /**
     * @inheritdoc
     */
    public function getOrderValueFrom(): ?float
    {
        $valFrom = $this->getData(self::ORDER_VALUE_FROM);
        return $valFrom !== null ? (float)$valFrom : null;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValueFrom(float $orderValueFrom): RateInterface
    {
        return $this->setData(self::ORDER_VALUE_FROM, $orderValueFrom);
    }

    /**
     * @inheritdoc
     */
    public function getOrderValueTo(): ?float
    {
        $valTo = $this->getData(self::ORDER_VALUE_TO);
        return $valTo !== null ? (float)$valTo : null;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValueTo(?float $orderValueTo): RateInterface
    {
        return $this->setData(self::ORDER_VALUE_TO, $orderValueTo);
    }

    /**
     * @inheritdoc
     */
    public function getCharge(): ?float
    {
        $charge = $this->getData(self::CHARGE);
        return $charge !== null ? (float)$charge : null;
    }

    /**
     * @inheritdoc
     */
    public function setCharge(float $charge): RateInterface
    {
        return $this->setData(self::CHARGE, $charge);
    }

    /**
     * @inheritdoc
     */
    public function getOversizedSurcharge(): ?float
    {
        $surcharge = $this->getData(self::OVERSIZED_SURCHARGE);
        return $surcharge !== null ? (float)$surcharge : null;
    }

    /**
     * @inheritdoc
     */
    public function setOversizedSurcharge(?float $oversizedSurcharge): RateInterface
    {
        return $this->setData(self::OVERSIZED_SURCHARGE, $oversizedSurcharge);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?RateExtensionInterface
    {
        $extension = $this->_getExtensionAttributes();
        return $extension instanceof RateExtensionInterface ? $extension : null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(RateExtensionInterface $extensionAttributes): RateInterface
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }
}
