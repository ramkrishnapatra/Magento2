<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class BookingEnquiry extends AbstractDb
{
    public const MAIN_TABLE = 'booking_enquiry';
    public const ID_FIELD_NAME = 'entity_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
