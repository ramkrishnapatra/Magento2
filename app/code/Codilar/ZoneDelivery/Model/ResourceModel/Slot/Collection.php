<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel\Slot;

use Codilar\ZoneDelivery\Model\ResourceModel\Slot as SlotResource;
use Codilar\ZoneDelivery\Model\Slot as SlotModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * Collection for delivery time slot entity
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
        $this->_init(SlotModel::class, SlotResource::class);
    }
}
