<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel\Pincode;

use Codilar\ZoneDelivery\Model\Pincode as PincodeModel;
use Codilar\ZoneDelivery\Model\ResourceModel\Pincode as PincodeResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * Collection for postal code to zone mapping entity
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
        $this->_init(PincodeModel::class, PincodeResource::class);
    }
}
