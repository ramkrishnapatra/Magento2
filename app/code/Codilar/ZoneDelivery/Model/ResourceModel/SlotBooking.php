<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel;

use Codilar\ZoneDelivery\Api\Data\SlotBookingInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class SlotBooking
 * Resource model handling slot reservations with pessimistic concurrency locking
 */
class SlotBooking extends AbstractDb
{
    /**
     * Initialize main table and primary key
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('magecafe_delivery_slot_booking', 'booking_id');
    }

    /**
     * Count confirmed and unexpired reserved bookings for a slot on a target date
     *
     * @param int $slotId
     * @param string $deliveryDate
     * @param bool $forUpdate
     * @return int
     */
    public function getActiveBookingCount(int $slotId, string $deliveryDate, bool $forUpdate = false): int
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), [new \Zend_Db_Expr('COUNT(*)')])
            ->where('slot_id = :slot_id')
            ->where('delivery_date = :delivery_date')
            ->where('status IN (?)', [
                SlotBookingInterface::STATUS_RESERVED,
                SlotBookingInterface::STATUS_CONFIRMED
            ]);

        if ($forUpdate) {
            $select->forUpdate(true);
        }

        $bind = [
            'slot_id' => $slotId,
            'delivery_date' => $deliveryDate
        ];

        return (int)$connection->fetchOne($select, $bind);
    }

    /**
     * Release existing temporary reservation for a given quote before assigning a new one
     *
     * @param int $quoteId
     * @return int Number of affected rows
     */
    public function releaseQuoteReservation(int $quoteId): int
    {
        $connection = $this->getConnection();
        return $connection->update(
            $this->getMainTable(),
            ['status' => SlotBookingInterface::STATUS_CANCELLED],
            [
                'quote_id = ?' => $quoteId,
                'status = ?' => SlotBookingInterface::STATUS_RESERVED
            ]
        );
    }

    /**
     * Release expired temporary reservations past the timeout window (BR-04, BR-05)
     *
     * @param string $cutoffTimestamp Formatted as Y-m-d H:i:s
     * @return int Number of expired rows marked cancelled
     */
    public function cancelExpiredReservations(string $cutoffTimestamp): int
    {
        $connection = $this->getConnection();
        return $connection->update(
            $this->getMainTable(),
            ['status' => SlotBookingInterface::STATUS_CANCELLED],
            [
                'status = ?' => SlotBookingInterface::STATUS_RESERVED,
                'reserved_at < ?' => $cutoffTimestamp
            ]
        );
    }

    /**
     * Fetch active booking record tied to a quote
     *
     * @param int $quoteId
     * @return array|false
     */
    public function getActiveBookingByQuoteId(int $quoteId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('quote_id = :quote_id')
            ->where('status = :status')
            ->order('booking_id DESC')
            ->limit(1);

        return $connection->fetchRow($select, [
            'quote_id' => $quoteId,
            'status' => SlotBookingInterface::STATUS_RESERVED
        ]);
    }
}
