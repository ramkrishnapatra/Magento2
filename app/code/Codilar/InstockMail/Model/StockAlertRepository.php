<?php
namespace Codilar\InstockMail\Model;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Codilar\InstockMail\Api\StockAlertRepositoryInterface;
use Codilar\InstockMail\Model\ResourceModel\StockAlert as ResourceModel;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class StockAlertRepository implements StockAlertRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected ResourceModel $resource;

    /**
     * @var StockAlertFactory
     */
    protected \Codilar\InstockMail\Model\StockAlertFactory $alertFactory;

    /**
     * @param ResourceModel $resource
     * @param StockAlertFactory $alertFactory
     */
    public function __construct(
        ResourceModel $resource,
        StockAlertFactory $alertFactory
    ) {
        $this->resource = $resource;
        $this->alertFactory = $alertFactory;
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
        $alert = $this->alertFactory->create();
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
