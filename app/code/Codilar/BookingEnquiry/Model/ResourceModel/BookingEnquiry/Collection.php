<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry;

use Codilar\BookingEnquiry\Model\BookingEnquiry as Model;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ResourceModel::ID_FIELD_NAME;

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
