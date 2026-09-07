<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Enquiry extends AbstractDb
{
    /**
     * Define main table and primary key
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('codilar_product_enquiry', 'entity_id');
    }
}
