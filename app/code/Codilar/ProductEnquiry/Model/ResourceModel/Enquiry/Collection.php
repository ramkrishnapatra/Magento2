<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model\ResourceModel\Enquiry;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codilar\ProductEnquiry\Model\Enquiry as Model;
use Codilar\ProductEnquiry\Model\ResourceModel\Enquiry as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Define model and resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
