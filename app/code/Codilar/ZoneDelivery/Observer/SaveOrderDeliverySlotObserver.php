<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class SaveOrderDeliverySlotObserver implements ObserverInterface
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Transfer slot attributes from Quote into Order model memory before insert
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

        if (!$order || !$quote) {
            return;
        }

        $slotId = $quote->getData('delivery_slot_id');
        $deliveryDate = $quote->getData('delivery_date');

        if (!empty($slotId)) {
            $order->setData('delivery_slot_id', (int)$slotId);
        }

        if (!empty($deliveryDate)) {
            $order->setData('delivery_date', (string)$deliveryDate);
        }

        $this->logger->info(sprintf(
            'MageCafe_ZoneDelivery: Set delivery slot %s and date %s onto order object for quote %s.',
            (string)$slotId,
            (string)$deliveryDate,
            (string)$quote->getId()
        ));
    }
}
