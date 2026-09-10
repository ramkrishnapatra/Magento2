<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model;

use Codilar\ProductEnquiry\Api\Data\EnquiryInterface;
use Codilar\ProductEnquiry\Api\EnquiryRepositoryInterface;
use Codilar\ProductEnquiry\Model\ResourceModel\Enquiry as EnquiryResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

class EnquiryRepository implements EnquiryRepositoryInterface
{
    protected EnquiryFactory $enquiryFactory;
    protected EnquiryResource $enquiryResource;

    public function __construct(
        EnquiryFactory $enquiryFactory,
        EnquiryResource $enquiryResource
    ) {
        $this->enquiryFactory = $enquiryFactory;
        $this->enquiryResource = $enquiryResource;
    }

    public function create(): EnquiryInterface
    {
        return $this->enquiryFactory->create();
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(EnquiryInterface $enquiry): EnquiryInterface
    {
        if (!$enquiry instanceof AbstractModel) {
            throw new CouldNotSaveException(
                __('Expected instance of %1, got %2', AbstractModel::class, get_class($enquiry))
            );
        }

        try {
            $this->enquiryResource->save($enquiry);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save enquiry: %1', $e->getMessage()));
        }

        return $enquiry;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById($id): EnquiryInterface
    {
        $enquiry = $this->create();
        $this->enquiryResource->load($enquiry, $id);

        if (!$enquiry->getId()) {
            throw new NoSuchEntityException(__('Enquiry with ID "%1" does not exist.', $id));
        }

        return $enquiry;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(EnquiryInterface $enquiry): bool
    {
        if (!$enquiry instanceof AbstractModel) {
            throw new CouldNotDeleteException(
                __('Expected instance of %1, got %2', AbstractModel::class, get_class($enquiry))
            );
        }

        try {
            $this->enquiryResource->delete($enquiry);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete enquiry: %1', $e->getMessage()));
        }

        return true;
    }
}
