<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Rate
 * Resource model for delivery rate tier entity
 */
class Rate extends AbstractDb
{
    /**
     * Initialize main table and primary key
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('magecafe_delivery_rate', 'rate_id');
    }

    /**
     * Fetch matching rate tier by zone, website, weight, and order value
     *
     * @param int $zoneId
     * @param int $websiteId
     * @param float $weight
     * @param float $orderValue
     * @return array|false
     */
    public function getMatchingRate(int $zoneId, int $websiteId, float $weight, float $orderValue)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('zone_id = :zone_id')
            ->where('website_id IN (0, :website_id)')
            ->where('weight_from <= :weight')
            ->where('weight_to > :weight')
            ->where('order_value_from <= :order_value')
            ->where('order_value_to > :order_value')
            ->order('website_id DESC')
            ->limit(1);

        $bind = [
            'zone_id' => $zoneId,
            'website_id' => $websiteId,
            'weight' => $weight,
            'order_value' => $orderValue
        ];

        return $connection->fetchRow($select, $bind);
    }
}
