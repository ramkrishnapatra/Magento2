<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\SlotExtensionInterface;
use Codilar\ZoneDelivery\Api\Data\SlotInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Slot
 * Model representing delivery time slot entity
 */
class Slot extends AbstractExtensibleModel implements SlotInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'codilar_delivery_slot';

    /**
     * Event object parameter name
     *
     * @var string
     */
    protected $_eventObject = 'slot';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\Codilar\ZoneDelivery\Model\ResourceModel\Slot::class);
    }

    /**
     * @inheritdoc
     */
    public function getSlotId(): ?int
    {
        $id = $this->getData(self::SLOT_ID);
        return $id !== null ? (int)$id : null;
    }

    /**
     * @inheritdoc
     */
    public function setSlotId($slotId): SlotInterface
    {
        return $this->setData(self::SLOT_ID, $slotId !== null ? (int)$slotId : null);
    }

    /**
     * @inheritdoc
     */
    public function getSlotReference(): ?string
    {
        return $this->getData(self::SLOT_REFERENCE);
    }

    /**
     * @inheritdoc
     */
    public function setSlotReference(string $slotReference): SlotInterface
    {
        return $this->setData(self::SLOT_REFERENCE, $slotReference);
    }

    /**
     * @inheritdoc
     */
    public function getStartTime(): ?string
    {
        return $this->getData(self::START_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setStartTime(string $startTime): SlotInterface
    {
        return $this->setData(self::START_TIME, $startTime);
    }

    /**
     * @inheritdoc
     */
    public function getEndTime(): ?string
    {
        return $this->getData(self::END_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setEndTime(string $endTime): SlotInterface
    {
        return $this->setData(self::END_TIME, $endTime);
    }

    /**
     * @inheritdoc
     */
    public function getCapacityPerDay(): ?int
    {
        $capacity = $this->getData(self::CAPACITY_PER_DAY);
        return $capacity !== null ? (int)$capacity : null;
    }

    /**
     * @inheritdoc
     */
    public function setCapacityPerDay(int $capacityPerDay): SlotInterface
    {
        return $this->setData(self::CAPACITY_PER_DAY, $capacityPerDay);
    }

    /**
     * @inheritdoc
     */
    public function getApplicableZones(): ?string
    {
        return $this->getData(self::APPLICABLE_ZONES);
    }

    /**
     * @inheritdoc
     */
    public function setApplicableZones(?string $applicableZones): SlotInterface
    {
        return $this->setData(self::APPLICABLE_ZONES, $applicableZones);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?SlotExtensionInterface
    {
        $extension = $this->_getExtensionAttributes();
        return $extension instanceof SlotExtensionInterface ? $extension : null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(SlotExtensionInterface $extensionAttributes): SlotInterface
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }
}
