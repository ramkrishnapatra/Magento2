<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Api;

use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Codilar\BookingEnquiry\Api\Data\RequestItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface;

interface BookingEnquiryRepositoryInterface
{
    /**
     * @param RequestItemInterface $enquiry
     * @return ResponseItemInterface
     */
    public function save(RequestItemInterface $enquiry): ResponseItemInterface;

    /**
     * @param int $entityId
     * @return ResponseGetDataItemInterface
     */
    public function getById(int $entityId): ResponseGetDataItemInterface;

    /**
     * @param int $id
     * @param string $status
     * @return ResponseItemInterface
     */
    public function updateStatus(int $id, string $status): ResponseItemInterface;

    /**
     * @return ResponseGetDataItemInterface
     */
    public function getQueueList(): ResponseGetDataItemInterface;

    /**
     * @param BookingEnquiryInterface $enquiry
     * @return ResponseItemInterface
     */
    public function delete(BookingEnquiryInterface $enquiry): ResponseItemInterface;

    /**
     * @param int $entityId
     * @return ResponseItemInterface
     */
    public function deleteById(int $entityId): ResponseItemInterface;

    /**
     * Get all booking enquiries for admin
     *
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface
     */
    public function getAdminQueueList(): ResponseGetDataItemInterface;
}
