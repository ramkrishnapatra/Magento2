<?php
namespace Codilar\InstockMail\Model;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Codilar\InstockMail\Model\ResourceModel\StockAlert as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class StockAlert extends AbstractModel implements StockAlertInterface
{
    public function setStoreId(int $storeId)
    {
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return int|null
     */
    public function getAlertId(): ?int
    {
        $val = $this->getData(self::ALERT_ID);
        return $val !== null ? (int)$val : null;
    }

    /**
     * @param int $alertId
     * @return StockAlertInterface
     */
    public function setAlertId(int $alertId): StockAlertInterface
    {
        return $this->setData(self::ALERT_ID, $alertId);
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     * @return StockAlertInterface
     */
    public function setCustomerId(int $customerId): StockAlertInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    /**
     * @param int $productId
     * @return StockAlertInterface
     */
    public function setProductId(int $productId): StockAlertInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return (string)$this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @param string $customerEmail
     * @return StockAlertInterface
     */
    public function setCustomerEmail(string $customerEmail): StockAlertInterface
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    /**
     * @param string $status
     * @return StockAlertInterface
     */
    public function setStatus(string $status): StockAlertInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return StockAlertInterface
     */
    public function setCreatedAt(string $createdAt): StockAlertInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string|null
     */
    public function getNotifiedAt(): ?string
    {
        $val = $this->getData(self::NOTIFIED_AT);
        return $val !== null ? (string)$val : null;
    }

    /**
     * @param string|null $notifiedAt
     * @return StockAlertInterface
     */
    public function setNotifiedAt(?string $notifiedAt): StockAlertInterface
    {
        return $this->setData(self::NOTIFIED_AT, $notifiedAt);
    }
}
