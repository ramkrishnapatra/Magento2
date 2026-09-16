<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel\SlotBooking;

use Codilar\ZoneDelivery\Model\ResourceModel\SlotBooking as SlotBookingResource;
use Codilar\ZoneDelivery\Model\SlotBooking as SlotBookingModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * Collection for delivery slot booking reservation entities
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
        $this->_init(SlotBookingModel::class, SlotBookingResource::class);
    }
}
