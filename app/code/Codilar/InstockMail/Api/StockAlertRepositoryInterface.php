<?php
namespace Codilar\InstockMail\Api;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

interface StockAlertRepositoryInterface
{
    /**
     * Subscribe customer/guest for out-of-stock notification
     *
     * @param int $productId
     * @param string $email
     * @param int|null $customerId
     * @return string
     * @throws LocalizedException
     */
    public function subscribe(int $productId, string $email, ?int $customerId = 0): string;
    /**
     * @param StockAlertInterface $alert
     * @return StockAlertInterface
     * @throws CouldNotSaveException
     */
    public function save(StockAlertInterface $alert): StockAlertInterface;

    /**
     * @param int $alertId
     * @return StockAlertInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $alertId): StockAlertInterface;

    /**
     * @param int $customerId
     * @param int $productId
     * @return StockAlertInterface|null
     */
    public function getPendingAlert(int $customerId, int $productId): ?StockAlertInterface;

    /**
     * @param int $productId
     * @return StockAlertInterface[]
     */
    public function getPendingAlertsByProduct(int $productId): array;

    /**
     * @param StockAlertInterface $alert
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(StockAlertInterface $alert): bool;
}
