<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\SlotBookingExtensionInterface;
use Codilar\ZoneDelivery\Api\Data\SlotBookingInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class SlotBooking
 * Model representing delivery slot booking reservation entity
 */
class SlotBooking extends AbstractExtensibleModel implements SlotBookingInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'codilar_delivery_slot_booking';

    /**
     * Event object parameter name
     *
     * @var string
     */
    protected $_eventObject = 'slot_booking';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\Codilar\ZoneDelivery\Model\ResourceModel\SlotBooking::class);
    }

    /**
     * @inheritdoc
     */
    public function getBookingId(): ?int
    {
        $id = $this->getData(self::BOOKING_ID);
        return $id !== null ? (int)$id : null;
    }

    /**
     * @inheritdoc
     */
    public function setBookingId($bookingId): SlotBookingInterface
    {
        return $this->setData(self::BOOKING_ID, $bookingId !== null ? (int)$bookingId : null);
    }

    /**
     * @inheritdoc
     */
    public function getSlotId(): ?int
    {
        $slotId = $this->getData(self::SLOT_ID);
        return $slotId !== null ? (int)$slotId : null;
    }

    /**
     * @inheritdoc
     */
    public function setSlotId(int $slotId): SlotBookingInterface
    {
        return $this->setData(self::SLOT_ID, $slotId);
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryDate(): ?string
    {
        return $this->getData(self::DELIVERY_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryDate(string $deliveryDate): SlotBookingInterface
    {
        return $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId(): ?int
    {
        $quoteId = $this->getData(self::QUOTE_ID);
        return $quoteId !== null ? (int)$quoteId : null;
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId(?int $quoteId): SlotBookingInterface
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId(): ?int
    {
        $orderId = $this->getData(self::ORDER_ID);
        return $orderId !== null ? (int)$orderId : null;
    }

    /**
     * @inheritdoc
     */
    public function setOrderId(?int $orderId): SlotBookingInterface
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $status): SlotBookingInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getReservedAt(): ?string
    {
        return $this->getData(self::RESERVED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setReservedAt(string $reservedAt): SlotBookingInterface
    {
        return $this->setData(self::RESERVED_AT, $reservedAt);
    }

    /**
     * @inheritdoc
     */
    public function getConfirmedAt(): ?string
    {
        return $this->getData(self::CONFIRMED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setConfirmedAt(?string $confirmedAt): SlotBookingInterface
    {
        return $this->setData(self::CONFIRMED_AT, $confirmedAt);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?SlotBookingExtensionInterface
    {
        $extension = $this->_getExtensionAttributes();
        return $extension instanceof SlotBookingExtensionInterface ? $extension : null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(SlotBookingExtensionInterface $extensionAttributes): SlotBookingInterface
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }
}
