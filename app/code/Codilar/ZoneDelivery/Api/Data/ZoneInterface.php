<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ZoneInterface
 * Represents a delivery zone entity
 */
interface ZoneInterface extends ExtensibleDataInterface
{
    public const ENTITY_ID = 'entity_id';
    public const ZONE_REFERENCE = 'zone_reference';
    public const DESCRIPTION = 'description';
    public const IS_REMOTE = 'is_remote';

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
     * Get zone reference code
     *
     * @return string|null
     */
    public function getZoneReference(): ?string;

    /**
     * Set zone reference code
     *
     * @param string $zoneReference
     * @return $this
     */
    public function setZoneReference(string $zoneReference): self;

    /**
     * Get zone description
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Set zone description
     *
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self;

    /**
     * Get remote status flag
     *
     * @return bool
     */
    public function getIsRemote(): bool;

    /**
     * Set remote status flag
     *
     * @param bool $isRemote
     * @return $this
     */
    public function setIsRemote(bool $isRemote): self;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Codilar\ZoneDelivery\Api\Data\ZoneExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Codilar\ZoneDelivery\Api\Data\ZoneExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Codilar\ZoneDelivery\Api\Data\ZoneExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Codilar\ZoneDelivery\Api\Data\ZoneExtensionInterface $extensionAttributes
    ): self;
}
