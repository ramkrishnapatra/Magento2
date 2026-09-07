<?php
namespace Codilar\ProductEnquiry\Model;

use Codilar\ProductEnquiry\Api\EnquiryRepositoryInterface;
use Codilar\ProductEnquiry\Api\Data\EnquiryInterface;
use Codilar\ProductEnquiry\Model\ResourceModel\Enquiry as EnquiryResource;
use Codilar\ProductEnquiry\Model\EnquiryFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class EnquiryRepository implements EnquiryRepositoryInterface
{
    protected $enquiryFactory;
    protected $enquiryResource;

    public function __construct(
        EnquiryFactory $enquiryFactory,
        EnquiryResource $enquiryResource
    ) {
        $this->enquiryFactory = $enquiryFactory;
        $this->enquiryResource = $enquiryResource;
    }

    public function create()
    {
        return $this->enquiryFactory->create();
    }

    public function save(EnquiryInterface $enquiry)
    {
        try {
            $this->enquiryResource->save($enquiry);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save enquiry: %1', $e->getMessage()));
        }
        return $enquiry;
    }

    public function getById($id)
    {
        $enquiry = $this->create();
        $this->enquiryResource->load($enquiry, $id);
        if (!$enquiry->getId()) {
            throw new NoSuchEntityException(__('Enquiry with ID "%1" does not exist.', $id));
        }
        return $enquiry;
    }

    public function delete(EnquiryInterface $enquiry)
    {
        try {
            $this->enquiryResource->delete($enquiry);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete enquiry: %1', $e->getMessage()));
        }
        return true;
    }
}
