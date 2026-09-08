<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Api;

use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface BookingEnquiryRepositoryInterface
{
    /**
     * @param BookingEnquiryInterface $enquiry
     * @return BookingEnquiryInterface
     * @throws CouldNotSaveException
     */
    public function save(BookingEnquiryInterface $enquiry): BookingEnquiryInterface;

    /**
     * @param int $entityId
     * @return BookingEnquiryInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): BookingEnquiryInterface;

    /**
     * @param BookingEnquiryInterface $enquiry
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BookingEnquiryInterface $enquiry): bool;

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $entityId): bool;
}
