<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Model;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Codilar\InstockMail\Api\StockAlertRepositoryInterface;
use Codilar\InstockMail\Model\ResourceModel\StockAlert as ResourceModel;
use Codilar\InstockMail\Model\ResourceModel\StockAlert\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class StockAlertRepository implements StockAlertRepositoryInterface
{
    protected ResourceModel $resource;
    protected StockAlertFactory $stockAlertFactory;
    protected CollectionFactory $collectionFactory;
    protected StoreManagerInterface $storeManager;
    protected DateTime $dateTime;
    protected LoggerInterface $logger;

    public function __construct(
        ResourceModel $resource,
        StockAlertFactory $stockAlertFactory,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->stockAlertFactory = $stockAlertFactory;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Subscribe customer/guest for out-of-stock notification
     *
     * @param int $productId
     * @param string $email
     * @param int|null $customerId
     * @return string
     * @throws LocalizedException
     */
    public function subscribe(int $productId, string $email, ?int $customerId = 0): string
    {
        $email = trim($email);

        if (!$productId || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new LocalizedException(
                __('Please provide a valid product and email address.')
            );
        }

        // Duplicate Check
        $existing = $this->collectionFactory->create()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('customer_email', $email)
            ->addFieldToFilter('status', StockAlertInterface::STATUS_PENDING)
            ->getFirstItem();

        if ($existing && $existing->getId()) {
            return __('You are already subscribed for this stock alert.')->render();
        }

        try {
            $storeId = (int)$this->storeManager->getStore()->getId();

            $alert = $this->stockAlertFactory->create();
            $alert->setCustomerId((int)$customerId);
            $alert->setCustomerEmail($email);
            $alert->setProductId($productId);
            $alert->setStoreId($storeId);
            $alert->setStatus(StockAlertInterface::STATUS_PENDING);
            $alert->setCreatedAt($this->dateTime->gmtDate());

            $this->save($alert);

            return __('Subscription successful! We will notify you when this item is back in stock.')->render();
        } catch (\Throwable $e) {
            $this->logger->critical('Stock Alert API Error: ' . $e->getMessage());
            throw new LocalizedException(
                __('Unable to process subscription: %1', $e->getMessage())
            );
        }
    }

    /**
     * @param StockAlertInterface $alert
     * @return StockAlertInterface
     * @throws CouldNotSaveException
     */
    public function save(StockAlertInterface $alert): StockAlertInterface
    {
        try {
            $this->resource->save($alert);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $alert;
    }

    /**
     * @param int $alertId
     * @return StockAlertInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $alertId): StockAlertInterface
    {
        $alert = $this->stockAlertFactory->create();
        $this->resource->load($alert, $alertId);
        if (!$alert->getId()) {
            throw new NoSuchEntityException(__('Stock alert with ID "%1" does not exist.', $alertId));
        }
        return $alert;
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @return StockAlertInterface|null
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getPendingAlert(int $customerId, int $productId): ?StockAlertInterface
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getMainTable();

        $select = $connection->select()
            ->from($tableName, 'alert_id')
            ->where('customer_id = ?', (int)$customerId)
            ->where('product_id = ?', (int)$productId)
            ->where('status = ?', StockAlertInterface::STATUS_PENDING)
            ->limit(1);

        $alertId = $connection->fetchOne($select);

        if ($alertId) {
            return $this->getById((int)$alertId);
        }

        return null;
    }

    /**
     * @param int $productId
     * @return StockAlertInterface[]
     * @throws LocalizedException
     */
    public function getPendingAlertsByProduct(int $productId): array
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getMainTable();

        $select = $connection->select()
            ->from($tableName, 'alert_id')
            ->where('product_id = ?', (int)$productId)
            ->where('status = ?', StockAlertInterface::STATUS_PENDING);

        $alertIds = $connection->fetchCol($select);
        $alerts = [];

        foreach ($alertIds as $alertId) {
            try {
                $alerts[] = $this->getById((int)$alertId);
            } catch (\Exception $e) {
                continue;
            }
        }

        return $alerts;
    }

    /**
     * @param StockAlertInterface $alert
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(StockAlertInterface $alert): bool
    {
        try {
            $this->resource->delete($alert);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }
}
