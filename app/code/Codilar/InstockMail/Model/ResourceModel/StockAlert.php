<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Stock Alert Resource Model
 */
class StockAlert extends AbstractDb
{
    /**
     * Main table name
     */
    public const MAIN_TABLE = 'codilar_custom_stock_alert';

    /**
     * Primary key field name
     */
    public const ID_FIELD_NAME = 'alert_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
