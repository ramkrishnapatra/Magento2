<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel\Rate;

use Codilar\ZoneDelivery\Model\Rate as RateModel;
use Codilar\ZoneDelivery\Model\ResourceModel\Rate as RateResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * Collection for delivery rate tier entity
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
        $this->_init(RateModel::class, RateResource::class);
    }
}
