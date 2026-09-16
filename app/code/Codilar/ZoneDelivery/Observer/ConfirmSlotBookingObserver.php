<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Observer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class ConfirmSlotBookingObserver implements ObserverInterface
{

    /**
     * @param ResourceConnection $resourceConnection
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private DateTime $dateTime,
        private LoggerInterface $logger
    ) {

    }

    /**
     * Concurrency-safe slot confirmation using pessimistic row locks (SELECT ... FOR UPDATE)
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var Order|null $order */
        $order = $observer->getEvent()->getData('order');

        /** @var Quote|null $quote */
        $quote = $observer->getEvent()->getData('quote');

        if (!$order) {
            return;
        }

        $shippingMethod = (string)$order->getShippingMethod();
        if (strpos($shippingMethod, 'zonedelivery') === false) {
            return;
        }

        $slotId = (int)($order->getData('delivery_slot_id') ?: ($quote ? $quote->getData('delivery_slot_id') : 0));
        $deliveryDate = (string)($order->getData('delivery_date') ?: ($quote ? $quote->getData('delivery_date') : ''));

        if (!$slotId || empty($deliveryDate)) {
            throw new LocalizedException(__('Please select a valid delivery slot and date to proceed.'));
        }

        $connection = $this->resourceConnection->getConnection();
        $slotTable = $this->resourceConnection->getTableName('magecafe_delivery_slot');
        $bookingTable = $this->resourceConnection->getTableName('magecafe_delivery_slot_booking');

        // শুরু হচ্ছে ডাটাবেস ট্রানজ্যাকশন
        $connection->beginTransaction();
        try {
            // ১. Pessimistic Row Lock: সংশ্লিষ্ট স্লট রো-কে এক্সক্লুসিভ লক করা
            $selectSlot = $connection->select()
                ->from($slotTable, ['capacity_per_day', 'is_active'])
                ->where('slot_id = ?', $slotId)
                ->forUpdate();

            $slotData = $connection->fetchRow($selectSlot);

            if (!$slotData || (int)$slotData['is_active'] !== 1) {
                throw new LocalizedException(__('The selected delivery slot is inactive or no longer available.'));
            }

            $totalCapacity = (int)$slotData['capacity_per_day'];

            // ২. এই স্লট ও ডেটে বর্তমানে কয়টি বুকিং কনফার্ম বা রিজার্ভ আছে তা লক সহ গণনা
            $selectCount = $connection->select()
                ->from($bookingTable, [new \Zend_Db_Expr('COUNT(*)')])
                ->where('slot_id = ?', $slotId)
                ->where('delivery_date = ?', $deliveryDate)
                ->where('status IN (?)', ['confirmed', 'reserved']);

            // যদি পূর্বেই এই কোটের জন্য একটি সাময়িক রিজার্ভেশন রেকর্ড থাকে, তবে সেটিকে বর্তমান গণনা থেকে বাদ দিতে হবে
            $quoteId = $quote ? (int)$quote->getId() : (int)$order->getQuoteId();
            if ($quoteId) {
                $selectCount->where('quote_id != ?', $quoteId);
            }

            $bookedCount = (int)$connection->fetchOne($selectCount);

            // ৩. কনকারেন্সি চেক: ক্যাপাসিটি শেষ হলে সাথে সাথে অর্ডার বাতিল (NFR-01 / BR-01)
            if ($bookedCount >= $totalCapacity) {
                throw new LocalizedException(
                    __('Selected delivery slot has just been fully booked by another customer. Please select another slot.')
                );
            }

            $currentTime = $this->dateTime->gmtDate();
            $orderId = (int)$order->getEntityId();

            // ৪. এক্সিস্টিং রিজার্ভেশন থাকলে কনফার্ম করা, না থাকলে সরাসরি ইনসার্ট করা
            $existingReservationSelect = $connection->select()
                ->from($bookingTable, ['booking_id'])
                ->where('quote_id = ?', $quoteId)
                ->where('slot_id = ?', $slotId)
                ->where('delivery_date = ?', $deliveryDate)
                ->limit(1);

            $existingBookingId = $connection->fetchOne($existingReservationSelect);

            if ($existingBookingId) {
                $connection->update(
                    $bookingTable,
                    [
                        'order_id' => $orderId,
                        'status' => 'confirmed',
                        'confirmed_at' => $currentTime
                    ],
                    ['booking_id = ?' => (int)$existingBookingId]
                );
            } else {
                $connection->insert($bookingTable, [
                    'slot_id' => $slotId,
                    'delivery_date' => $deliveryDate,
                    'quote_id' => $quoteId,
                    'order_id' => $orderId,
                    'status' => 'confirmed',
                    'reserved_at' => $currentTime,
                    'confirmed_at' => $currentTime
                ]);
            }

            // ট্রানজ্যাকশন সফলভাবে শেষ
            $connection->commit();

            $this->logger->info(sprintf(
                'MageCafe_ZoneDelivery: Slot booking confirmed for Order #%s (Slot %s, Date %s).',
                $order->getIncrementId(),
                (string)$slotId,
                $deliveryDate
            ));
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->error(sprintf(
                'MageCafe_ZoneDelivery: Concurrency/Booking failure on Order #%s: %s',
                $order->getIncrementId(),
                $e->getMessage()
            ));
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
