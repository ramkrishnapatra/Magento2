<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SlotBookingInterface
 * Represents a delivery slot booking reservation entity
 */
interface SlotBookingInterface extends ExtensibleDataInterface
{
    public const BOOKING_ID = 'booking_id';
    public const SLOT_ID = 'slot_id';
    public const DELIVERY_DATE = 'delivery_date';
    public const QUOTE_ID = 'quote_id';
    public const ORDER_ID = 'order_id';
    public const STATUS = 'status';
    public const RESERVED_AT = 'reserved_at';
    public const CONFIRMED_AT = 'confirmed_at';

    public const STATUS_RESERVED = 'reserved';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get booking ID
     *
     * @return int|null
     */
    public function getBookingId(): ?int;

    /**
     * Set booking ID
     *
     * @param int $bookingId
     * @return $this
     */
    public function setBookingId($bookingId): self;

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
    public function setSlotId(int $slotId): self;

    /**
     * Get target delivery date
     *
     * @return string|null
     */
    public function getDeliveryDate(): ?string;

    /**
     * Set target delivery date
     *
     * @param string $deliveryDate
     * @return $this
     */
    public function setDeliveryDate(string $deliveryDate): self;

    /**
     * Get quote ID
     *
     * @return int|null
     */
    public function getQuoteId(): ?int;

    /**
     * Set quote ID
     *
     * @param int|null $quoteId
     * @return $this
     */
    public function setQuoteId(?int $quoteId): self;

    /**
     * Get order ID
     *
     * @return int|null
     */
    public function getOrderId(): ?int;

    /**
     * Set order ID
     *
     * @param int|null $orderId
     * @return $this
     */
    public function setOrderId(?int $orderId): self;

    /**
     * Get booking status
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Set booking status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self;

    /**
     * Get reserved timestamp
     *
     * @return string|null
     */
    public function getReservedAt(): ?string;

    /**
     * Set reserved timestamp
     *
     * @param string $reservedAt
     * @return $this
     */
    public function setReservedAt(string $reservedAt): self;

    /**
     * Get confirmed timestamp
     *
     * @return string|null
     */
    public function getConfirmedAt(): ?string;

    /**
     * Set confirmed timestamp
     *
     * @param string|null $confirmedAt
     * @return $this
     */
    public function setConfirmedAt(?string $confirmedAt): self;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Codilar\ZoneDelivery\Api\Data\SlotBookingExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Codilar\ZoneDelivery\Api\Data\SlotBookingExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Codilar\ZoneDelivery\Api\Data\SlotBookingExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Codilar\ZoneDelivery\Api\Data\SlotBookingExtensionInterface $extensionAttributes
    ): self;
}
