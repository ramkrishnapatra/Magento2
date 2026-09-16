<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Observer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class TransferSlotToOrderObserver implements ObserverInterface
{

    /**
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private LoggerInterface $logger
    ) {

    }

    /**
     * Ensure slot_id and delivery_date are persisted directly to sales_order table
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Order|null $order */
        $order = $observer->getEvent()->getData('order');

        /** @var Quote|null $quote */
        $quote = $observer->getEvent()->getData('quote');

        if (!$order || !$quote || !(int)$order->getEntityId()) {
            return;
        }

        $slotId = $quote->getData('delivery_slot_id') ?: $order->getData('delivery_slot_id');
        $deliveryDate = $quote->getData('delivery_date') ?: $order->getData('delivery_date');

        if (!empty($slotId) && !empty($deliveryDate)) {
            try {
                $connection = $this->resourceConnection->getConnection();
                $salesOrderTable = $this->resourceConnection->getTableName('sales_order');

                $connection->update(
                    $salesOrderTable,
                    [
                        'delivery_slot_id' => (int)$slotId,
                        'delivery_date' => (string)$deliveryDate
                    ],
                    ['entity_id = ?' => (int)$order->getEntityId()]
                );

                $this->logger->info(sprintf(
                    'MageCafe_ZoneDelivery: Persisted slot %s and date %s into sales_order table for Order ID %s.',
                    (string)$slotId,
                    (string)$deliveryDate,
                    (string)$order->getEntityId()
                ));
            } catch (\Exception $e) {
                $this->logger->error(sprintf(
                    'MageCafe_ZoneDelivery: Failed direct update on sales_order for Order ID %s: %s',
                    (string)$order->getEntityId(),
                    $e->getMessage()
                ));
            }
        }
    }
}
