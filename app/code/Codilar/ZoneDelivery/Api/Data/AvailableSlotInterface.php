<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api\Data;

/**
 * Interface AvailableSlotInterface
 * Represents delivery slot data contract for REST API responses
 */
interface AvailableSlotInterface
{
    public const SLOT_ID = 'slot_id';
    public const SLOT_REFERENCE = 'slot_reference';
    public const START_TIME = 'start_time';
    public const END_TIME = 'end_time';
    public const IS_AVAILABLE = 'is_available';

    /**
     * Get Slot ID
     *
     * @return int
     */
    public function getSlotId(): int;

    /**
     * Set Slot ID
     *
     * @param int $slotId
     * @return $this
     */
    public function setSlotId(int $slotId): self;

    /**
     * Get Slot Reference Name
     *
     * @return string
     */
    public function getSlotReference(): string;

    /**
     * Set Slot Reference Name
     *
     * @param string $slotReference
     * @return $this
     */
    public function setSlotReference(string $slotReference): self;

    /**
     * Get Slot Start Time
     *
     * @return string
     */
    public function getStartTime(): string;

    /**
     * Set Slot Start Time
     *
     * @param string $startTime
     * @return $this
     */
    public function setStartTime(string $startTime): self;

    /**
     * Get Slot End Time
     *
     * @return string
     */
    public function getEndTime(): string;

    /**
     * Set Slot End Time
     *
     * @param string $endTime
     * @return $this
     */
    public function setEndTime(string $endTime): self;

    /**
     * Get Availability Status
     *
     * @return bool
     */
    public function getIsAvailable(): bool;

    /**
     * Set Availability Status
     *
     * @param bool $isAvailable
     * @return $this
     */
    public function setIsAvailable(bool $isAvailable): self;
}
