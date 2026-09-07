<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Model;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Codilar\InstockMail\Model\ResourceModel\StockAlert as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class StockAlert extends AbstractModel implements StockAlertInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function getAlertId(): ?int
    {
        $val = $this->getData(self::ALERT_ID);
        return $val !== null ? (int)$val : null;
    }

    public function setAlertId(int $alertId): StockAlertInterface
    {
        return $this->setData(self::ALERT_ID, $alertId);
    }

    public function getCustomerId(): int
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId(int $customerId): StockAlertInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    public function setProductId(int $productId): StockAlertInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function getCustomerEmail(): string
    {
        return (string)$this->getData(self::CUSTOMER_EMAIL);
    }

    public function setCustomerEmail(string $customerEmail): StockAlertInterface
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    public function setStatus(string $status): StockAlertInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): StockAlertInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getNotifiedAt(): ?string
    {
        $val = $this->getData(self::NOTIFIED_AT);
        return $val !== null ? (string)$val : null;
    }

    public function setNotifiedAt(?string $notifiedAt): StockAlertInterface
    {
        return $this->setData(self::NOTIFIED_AT, $notifiedAt);
    }
}
