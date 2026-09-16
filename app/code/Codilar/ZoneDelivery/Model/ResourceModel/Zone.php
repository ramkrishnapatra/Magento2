<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Zone
 * Resource model for delivery zone entity
 */
class Zone extends AbstractDb
{
    /**
     * Main table and primary key initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('magecafe_delivery_zone', 'entity_id');
    }
}
