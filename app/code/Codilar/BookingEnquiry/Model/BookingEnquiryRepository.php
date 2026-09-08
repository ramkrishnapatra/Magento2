<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model;

use Codilar\BookingEnquiry\Api\BookingEnquiryRepositoryInterface;
use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry as ResourceModel;
use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BookingEnquiryRepository implements BookingEnquiryRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private ResourceModel $resource;

    /**
     * @var BookingEnquiryFactory
     */
    private BookingEnquiryFactory $bookingEnquiryFactory;

    /**
     * @param ResourceModel $resource
     * @param BookingEnquiryFactory $bookingEnquiryFactory
     */
    public function __construct(
        ResourceModel $resource,
        BookingEnquiryFactory $bookingEnquiryFactory
    ) {
        $this->resource = $resource;
        $this->bookingEnquiryFactory = $bookingEnquiryFactory;
    }

    /**
     * @param BookingEnquiryInterface $enquiry
     * @return BookingEnquiryInterface
     * @throws CouldNotSaveException
     */
    public function save(BookingEnquiryInterface $enquiry): BookingEnquiryInterface
    {
        try {
            $this->resource->save($enquiry);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $enquiry;
    }

    /**
     * @param int $entityId
     * @return BookingEnquiryInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): BookingEnquiryInterface
    {
        $enquiry = $this->bookingEnquiryFactory->create();
        $this->resource->load($enquiry, $entityId);
        if (!$enquiry->getEntityId()) {
            throw new NoSuchEntityException(__('Booking enquiry with ID "%1" does not exist.', $entityId));
        }
        return $enquiry;
    }

    /**
     * @param BookingEnquiryInterface $enquiry
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BookingEnquiryInterface $enquiry): bool
    {
        try {
            $this->resource->delete($enquiry);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }
}
