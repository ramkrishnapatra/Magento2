<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\Data;

use Codilar\ZoneDelivery\Api\Data\AvailableSlotInterface;
use Magento\Framework\DataObject;

/**
 * Class AvailableSlot
 * Concrete implementation of AvailableSlotInterface
 */
class AvailableSlot extends DataObject implements AvailableSlotInterface
{
    /**
     * @inheritDoc
     */
    public function getSlotId(): int
    {
        return (int)$this->getData(self::SLOT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSlotId(int $slotId): AvailableSlotInterface
    {
        return $this->setData(self::SLOT_ID, $slotId);
    }

    /**
     * @inheritDoc
     */
    public function getSlotReference(): string
    {
        return (string)$this->getData(self::SLOT_REFERENCE);
    }

    /**
     * @inheritDoc
     */
    public function setSlotReference(string $slotReference): AvailableSlotInterface
    {
        return $this->setData(self::SLOT_REFERENCE, $slotReference);
    }

    /**
     * @inheritDoc
     */
    public function getStartTime(): string
    {
        return (string)$this->getData(self::START_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setStartTime(string $startTime): AvailableSlotInterface
    {
        return $this->setData(self::START_TIME, $startTime);
    }

    /**
     * @inheritDoc
     */
    public function getEndTime(): string
    {
        return (string)$this->getData(self::END_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setEndTime(string $endTime): AvailableSlotInterface
    {
        return $this->setData(self::END_TIME, $endTime);
    }

    /**
     * @inheritDoc
     */
    public function getIsAvailable(): bool
    {
        return (bool)$this->getData(self::IS_AVAILABLE);
    }

    /**
     * @inheritDoc
     */
    public function setIsAvailable(bool $isAvailable): AvailableSlotInterface
    {
        return $this->setData(self::IS_AVAILABLE, $isAvailable);
    }
}
