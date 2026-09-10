<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Api\Data;

interface ResponseGetDataItemInterface extends ResponseItemInterface
{
    public const DATA = 'data_payload';

    /**
     * Get Response Data Payload
     *
     * @return \Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface[]|mixed
     */
    public function getDataPayload();

    /**
     * Set Response Data Payload
     *
     * @param mixed $data
     * @return $this
     */
    public function setDataPayload($data): self;
}
