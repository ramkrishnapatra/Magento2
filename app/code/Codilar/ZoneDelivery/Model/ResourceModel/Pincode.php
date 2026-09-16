<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Pincode
 * Resource model for postal code to zone mapping
 */
class Pincode extends AbstractDb
{
    /**
     * Initialize main table and primary key
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('magecafe_delivery_pincode', 'entity_id');
    }

    /**
     * Resolve Zone ID directly by postal code for fast shipping calculation
     *
     * @param string $pincode
     * @return int|null
     */
    public function getZoneIdByPincode(string $pincode): ?int
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'zone_id')
            ->where('pincode = :pincode')
            ->limit(1);

        $zoneId = $connection->fetchOne($select, ['pincode' => trim($pincode)]);
        return $zoneId !== false && $zoneId !== null ? (int)$zoneId : null;
    }
}
