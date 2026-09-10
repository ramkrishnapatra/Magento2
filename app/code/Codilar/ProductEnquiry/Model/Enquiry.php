<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model;

use Codilar\ProductEnquiry\Api\Data\EnquiryInterface;
use Codilar\ProductEnquiry\Model\ResourceModel\Enquiry as EnquiryResource;
use Magento\Framework\Model\AbstractModel;

class Enquiry extends AbstractModel implements EnquiryInterface
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(EnquiryResource::class);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        $id = $this->getData(self::ENTITY_ID);
        return $id !== null ? (int) $id : null;
    }

    /**
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->getData(self::SKU);
    }

    /**
     * @param string $sku
     * @return EnquiryInterface
     */
    public function setSku(string $sku): EnquiryInterface
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return EnquiryInterface
     */
    public function setName(string $name): EnquiryInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @param string $email
     * @return EnquiryInterface
     */
    public function setEmail(string $email): EnquiryInterface
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->getData(self::ADDRESS);
    }

    /**
     * @param string $address
     * @return EnquiryInterface
     */
    public function setAddress(string $address): EnquiryInterface
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * @return int|null
     */
    public function getQty(): ?int
    {
        $qty = $this->getData(self::QTY);
        return $qty !== null ? (int) $qty : null;
    }

    /**
     * @param int $qty
     * @return EnquiryInterface
     */
    public function setQty(int $qty): EnquiryInterface
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return EnquiryInterface
     */
    public function setCreatedAt(string $createdAt): EnquiryInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
