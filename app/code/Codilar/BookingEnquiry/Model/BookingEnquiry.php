<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model;

use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class BookingEnquiry extends AbstractModel implements BookingEnquiryInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return int|null
     */
    public function getEntityId()
    {
        $id = $this->getData(self::ENTITY_ID);
        return $id !== null ? (int)$id : null;
    }

    /**
     * @param int|null $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        $phone = $this->getData(self::PHONE_NUMBER);
        return $phone !== null ? (string)$phone : null;
    }

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber(string $phoneNumber): BookingEnquiryInterface
    {
        return $this->setData(self::PHONE_NUMBER, $phoneNumber);
    }

    /**
     * @return string|null
     */
    public function getPreferredSlot(): ?string
    {
        $slot = $this->getData(self::PREFERRED_SLOT);
        return $slot !== null ? (string)$slot : null;
    }

    /**
     * @param string $preferredSlot
     * @return $this
     */
    public function setPreferredSlot(string $preferredSlot): BookingEnquiryInterface
    {
        return $this->setData(self::PREFERRED_SLOT, $preferredSlot);
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        $status = $this->getData(self::STATUS);
        return $status !== null ? (string)$status : null;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): BookingEnquiryInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        $date = $this->getData(self::CREATED_AT);
        return $date !== null ? (string)$date : null;
    }

    /**
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt(?string $createdAt): BookingEnquiryInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
