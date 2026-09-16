<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SlotInterface
 * Represents a delivery time slot entity
 */
interface SlotInterface extends ExtensibleDataInterface
{
    public const SLOT_ID = 'slot_id';
    public const SLOT_REFERENCE = 'slot_reference';
    public const START_TIME = 'start_time';
    public const END_TIME = 'end_time';
    public const CAPACITY_PER_DAY = 'capacity_per_day';
    public const APPLICABLE_ZONES = 'applicable_zones';

    /**
     * Get slot ID
     *
     * @return int|null
     */
    public function getSlotId(): ?int;

    /**
     * Set slot ID
     *
     * @param int $slotId
     * @return $this
     */
    public function setSlotId($slotId): self;

    /**
     * Get slot reference code
     *
     * @return string|null
     */
    public function getSlotReference(): ?string;

    /**
     * Set slot reference code
     *
     * @param string $slotReference
     * @return $this
     */
    public function setSlotReference(string $slotReference): self;

    /**
     * Get slot start time
     *
     * @return string|null
     */
    public function getStartTime(): ?string;

    /**
     * Set slot start time
     *
     * @param string $startTime
     * @return $this
     */
    public function setStartTime(string $startTime): self;

    /**
     * Get slot end time
     *
     * @return string|null
     */
    public function getEndTime(): ?string;

    /**
     * Set slot end time
     *
     * @param string $endTime
     * @return $this
     */
    public function setEndTime(string $endTime): self;

    /**
     * Get maximum booking capacity per day
     *
     * @return int|null
     */
    public function getCapacityPerDay(): ?int;

    /**
     * Set maximum booking capacity per day
     *
     * @param int $capacityPerDay
     * @return $this
     */
    public function setCapacityPerDay(int $capacityPerDay): self;

    /**
     * Get applicable zones definition
     *
     * @return string|null
     */
    public function getApplicableZones(): ?string;

    /**
     * Set applicable zones definition
     *
     * @param string|null $applicableZones
     * @return $this
     */
    public function setApplicableZones(?string $applicableZones): self;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Codilar\ZoneDelivery\Api\Data\SlotExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Codilar\ZoneDelivery\Api\Data\SlotExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Codilar\ZoneDelivery\Api\Data\SlotExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Codilar\ZoneDelivery\Api\Data\SlotExtensionInterface $extensionAttributes
    ): self;
}
