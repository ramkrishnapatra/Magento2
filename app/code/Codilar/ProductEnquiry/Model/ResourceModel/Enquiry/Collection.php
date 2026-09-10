<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model\ResourceModel\Enquiry;

use Codilar\ProductEnquiry\Model\Enquiry as Model;
use Codilar\ProductEnquiry\Model\ResourceModel\Enquiry as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model and resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
