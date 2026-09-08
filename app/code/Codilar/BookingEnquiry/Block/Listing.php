<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Block;

use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry\Collection;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry\CollectionFactory;
use Magento\Framework\View\Element\Template;

class Listing extends Template
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Fetch bookings collection ordered by created_at ASC (FIFO)
     *
     * @return Collection
     */
    public function getEnquiries(): Collection
    {
        $collection = $this->collectionFactory->create();
        $collection->setOrder('created_at', Collection::SORT_ORDER_ASC);
        return $collection;
    }
}
