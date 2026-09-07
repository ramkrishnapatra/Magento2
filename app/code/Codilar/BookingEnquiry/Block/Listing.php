<?php
namespace Codilar\BookingEnquiry\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\ResourceConnection;

class Listing extends Template
{
    protected ResourceConnection $resource;

    public function __construct(Template\Context $context, ResourceConnection $resource, array $data = [])
    {
        parent::__construct($context, $data);
        $this->resource = $resource;
    }

    /**
     * Fetch bookings ordered by created_at ASC (FIFO)
     */
    public function getEnquiries(): array
    {
        try {
            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('booking_enquiry');

            $select = $connection->select()
                ->from($tableName)
                ->order('created_at ASC');

            return $connection->fetchAll($select);
        } catch (\Exception $e) {
            return [];
        }
    }
}
