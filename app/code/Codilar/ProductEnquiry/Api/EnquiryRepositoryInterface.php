<?php
namespace Codilar\ProductEnquiry\Api;

use Codilar\ProductEnquiry\Api\Data\EnquiryInterface;

interface EnquiryRepositoryInterface
{
    /**
     * Save enquiry data
     *
     * @param EnquiryInterface $enquiry
     * @return EnquiryInterface
     */
    public function save(EnquiryInterface $enquiry): EnquiryInterface;

    /**
     * Create blank enquiry instance (Controller directly factory create nahi karega)
     *
     * @return EnquiryInterface
     */
    public function create(): EnquiryInterface;

    /**
     * Get enquiry by ID
     *
     * @param int $id
     * @return EnquiryInterface
     */
    public function getById($id): EnquiryInterface;

    /**
     * Delete enquiry
     *
     * @param EnquiryInterface $enquiry
     * @return bool
     */
    public function delete(EnquiryInterface $enquiry): bool;
}
