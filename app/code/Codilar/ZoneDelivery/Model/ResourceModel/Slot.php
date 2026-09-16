<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Slot
 * Resource model for delivery time slot entity
 */
class Slot extends AbstractDb
{
    /**
     * Initialize main table and primary key
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('magecafe_delivery_slot', 'slot_id');
    }

    /**
     * Fetch active delivery slots applicable for a specific zone
     *
     * @param int $zoneId
     * @return array
     */
    public function getSlotsByZone(int $zoneId): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('applicable_zones = ?', '*')
            ->orWhere(new \Zend_Db_Expr('FIND_IN_SET(' . (int)$zoneId . ', applicable_zones)'))
            ->order('start_time ASC');

        return $connection->fetchAll($select);
    }
}
