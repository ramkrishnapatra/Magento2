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
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        $val = $this->getData(self::ENTITY_ID);
        return $val !== null ? (int)$val : null;
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self
    {
        return $this->setData(self::ENTITY_ID, (int)$entityId);
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        $val = $this->getData(self::PHONE_NUMBER);
        return $val !== null ? (string)$val : null;
    }

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber(string $phoneNumber): self
    {
        return $this->setData(self::PHONE_NUMBER, $phoneNumber);
    }

    /**
     * @return string|null
     */
    public function getPreferredSlot(): ?string
    {
        $val = $this->getData(self::PREFERRED_SLOT);
        return $val !== null ? (string)$val : null;
    }

    /**
     * @param string $preferredSlot
     * @return $this
     */
    public function setPreferredSlot(string $preferredSlot): self
    {
        return $this->setData(self::PREFERRED_SLOT, $preferredSlot);
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        $val = $this->getData(self::STATUS);
        return $val !== null ? (string)$val : null;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        $val = $this->getData(self::CREATED_AT);
        return $val !== null ? (string)$val : null;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
