<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model\Api;

use Codilar\BookingEnquiry\Api\Data\RequestItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseItemInterface;
use Magento\Framework\DataObject;

/**
 * Class RequestItem
 *
 * Data Transfer Object (DTO) representing incoming payload for booking enquiry.
 */
class RequestItem extends DataObject implements RequestItemInterface
{

    /**
     * Get list of items
     *
     * @return ResponseItemInterface[]|null
     */
    public function getItems(): ?array
    {
        return $this->_getData(self::ITEMS);
    }

    /**
     * Set list of items
     *
     * @param ResponseItemInterface[]|null $items
     * @return $this
     */
    public function setItems(?array $items): self
    {
        return $this->setData(self::ITEMS, $items);
    }
    /**
     * Get customer phone number
     *
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return (string)$this->_getData(self::PHONE_NUMBER);
    }

    /**
     * Set customer phone number
     *
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber(string $phoneNumber): self
    {
        return $this->setData(self::PHONE_NUMBER, $phoneNumber);
    }

    /**
     * Get customer preferred callback slot
     *
     * @return string
     */
    public function getPreferredSlot(): string
    {
        return (string)$this->_getData(self::PREFERRED_SLOT);
    }

    /**
     * Set customer preferred callback slot
     *
     * @param string $preferredSlot
     * @return $this
     */
    public function setPreferredSlot(string $preferredSlot): self
    {
        return $this->setData(self::PREFERRED_SLOT, $preferredSlot);
    }
}
