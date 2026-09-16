<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Api;

use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Codilar\BookingEnquiry\Api\Data\RequestItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseItemInterface;

interface BookingEnquiryRepositoryInterface
{
    /**
     * @param \Codilar\BookingEnquiry\Api\Data\RequestItemInterface $enquiry
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseItemInterface
     */
    public function save(RequestItemInterface $enquiry): ResponseItemInterface;

    /**
     * @param int $entityId
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface
     */
    public function getById(int $entityId): ResponseGetDataItemInterface;

    /**
     * @param int $id
     * @param string $status
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseItemInterface
     */
    public function updateStatus(int $id, string $status): ResponseItemInterface;

    /**
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface
     */
    public function getQueueList(): ResponseGetDataItemInterface;

    /**
     * @param \Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface $enquiry
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseItemInterface
     */
    public function delete(BookingEnquiryInterface $enquiry): ResponseItemInterface;

    /**
     * @param int $entityId
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseItemInterface
     */
    public function deleteById(int $entityId): ResponseItemInterface;

    /**
     * @return \Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface
     */
    public function getAdminQueueList(): ResponseGetDataItemInterface;
}
