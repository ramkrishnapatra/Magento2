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
    public function save(EnquiryInterface $enquiry);

    /**
     * Create blank enquiry instance (Controller directly factory create nahi karega)
     *
     * @return EnquiryInterface
     */
    public function create();

    /**
     * Get enquiry by ID
     *
     * @param int $id
     * @return EnquiryInterface
     */
    public function getById($id);

    /**
     * Delete enquiry
     *
     * @param EnquiryInterface $enquiry
     * @return bool
     */
    public function delete(EnquiryInterface $enquiry);
}
