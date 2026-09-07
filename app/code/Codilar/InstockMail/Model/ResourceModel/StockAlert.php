<?php
namespace Codilar\InstockMail\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StockAlert extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('codilar_custom_stock_alert', 'alert_id');
    }
}
