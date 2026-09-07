<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Api\Data;

interface StockAlertInterface
{
    // do not add return type on Constants , simple assignment
    public const ALERT_ID = 'alert_id';
    public const CUSTOMER_ID = 'customer_id';
    public const PRODUCT_ID = 'product_id';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const NOTIFIED_AT = 'notified_at';

    public const STATUS_PENDING = 'pending';
    public const STATUS_HANDLED = 'handled';

    /**
     * @return int|null
     */
    public function getAlertId(): ?int;

    /**
     * @param int $alertId
     * @return $this
     */
    public function setAlertId(int $alertId): self;

    /**
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId): self;

    /**
     * @return int
     */
    public function getProductId(): int;

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId): self;

    /**
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail(string $customerEmail): self;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * @return string|null
     */
    public function getNotifiedAt(): ?string;

    /**
     * @param string|null $notifiedAt
     * @return $this
     */
    public function setNotifiedAt(?string $notifiedAt): self;
}
