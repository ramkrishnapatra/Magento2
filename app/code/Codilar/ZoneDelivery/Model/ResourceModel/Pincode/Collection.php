<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\ResourceModel\Pincode;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            \Codilar\ZoneDelivery\Model\Pincode::class,
            \Codilar\ZoneDelivery\Model\ResourceModel\Pincode::class
        );
    }

    public function addPincodeRangeFilter($pincode)
    {
        $numericPincode = (int) $pincode;
        $this->addFieldToFilter('pincode_from', ['lteq' => $numericPincode]);
        $this->addFieldToFilter('pincode_to', ['gteq' => $numericPincode]);
        return $this;
    }
}
