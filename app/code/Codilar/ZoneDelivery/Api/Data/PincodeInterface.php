<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface PincodeInterface
 * Represents a delivery postal code to zone mapping
 */
interface PincodeInterface extends ExtensibleDataInterface
{
    public const ENTITY_ID = 'entity_id';
    public const PINCODE_FROM = 'pincode_from';
    public const PINCODE_TO = 'pincode_to';
    public const ZONE_ID = 'zone_id';

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Set entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self;

    /**
     * @return mixed
     */
    public function getPincodeFrom();

    /**
     * @param $pincodeFrom
     * @return mixed
     */
    public function setPincodeFrom($pincodeFrom);

    /**
     * @return mixed
     */
    public function getPincodeTo();

    /**
     * @param $pincodeTo
     * @return mixed
     */
    public function setPincodeTo($pincodeTo);
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
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Codilar\ZoneDelivery\Api\Data\PincodeExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Codilar\ZoneDelivery\Api\Data\PincodeExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Codilar\ZoneDelivery\Api\Data\PincodeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Codilar\ZoneDelivery\Api\Data\PincodeExtensionInterface $extensionAttributes
    ): self;
}
