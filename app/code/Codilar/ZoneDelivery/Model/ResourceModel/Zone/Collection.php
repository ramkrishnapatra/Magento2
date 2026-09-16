<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel\Zone;

use Codilar\ZoneDelivery\Model\ResourceModel\Zone as ZoneResource;
use Codilar\ZoneDelivery\Model\Zone as ZoneModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * Collection for delivery zone entity
 */
class Collection extends AbstractCollection
{
    /**
     * Standard collection initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ZoneModel::class, ZoneResource::class);
    }
}
