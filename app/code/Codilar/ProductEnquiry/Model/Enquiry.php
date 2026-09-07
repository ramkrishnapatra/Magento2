<?php
namespace Codilar\ProductEnquiry\Model;

use Magento\Framework\Model\AbstractModel;
use Codilar\ProductEnquiry\Api\Data\EnquiryInterface;
use Codilar\ProductEnquiry\Model\ResourceModel\Enquiry as EnquiryResource;

class Enquiry extends AbstractModel implements EnquiryInterface
{
    protected function _construct()
    {
        $this->_init(EnquiryResource::class);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
