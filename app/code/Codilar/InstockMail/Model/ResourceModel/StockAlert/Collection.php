<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Model\ResourceModel\StockAlert;

use Codilar\InstockMail\Model\ResourceModel\StockAlert as StockAlertResourceModel;
use Codilar\InstockMail\Model\StockAlert as StockAlertModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'alert_id';

    /**
     * Define model & resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(StockAlertModel::class, StockAlertResourceModel::class);
    }
}
