<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\PincodeInterface;
use Codilar\ZoneDelivery\Api\PincodeRepositoryInterface;
use Codilar\ZoneDelivery\Model\ResourceModel\Pincode as PincodeResource;
use Codilar\ZoneDelivery\Model\ResourceModel\Pincode\CollectionFactory as PincodeCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PincodeRepository
 * Implementation of PincodeRepositoryInterface handling postal code to zone mapping persistence
 */
class PincodeRepository implements PincodeRepositoryInterface
{


    /**
     * @param PincodeResource $resource
     * @param PincodeFactory $pincodeFactory
     * @param PincodeCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private PincodeResource $resource,
        private PincodeFactory $pincodeFactory,
        private PincodeCollectionFactory $collectionFactory,
        private SearchResultsInterfaceFactory $searchResultsFactory,
        private CollectionProcessorInterface $collectionProcessor
    ) {

    }

    /**
     * @inheritdoc
     */
    public function save(PincodeInterface $pincode): PincodeInterface
    {
        try {
            $this->resource->save($pincode);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save postal code mapping: %1', $exception->getMessage()),
                $exception
            );
        }
        return $pincode;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $pincodeId): PincodeInterface
    {
        /** @var Pincode $pincode */
        $pincode = $this->pincodeFactory->create();
        $this->resource->load($pincode, $pincodeId);
        if (!$pincode->getId()) {
            throw new NoSuchEntityException(
                __('Postal code mapping with ID "%1" does not exist.', $pincodeId)
            );
        }
        return $pincode;
    }

    /**
     * @inheritdoc
     */
    public function getByPincode(string $pincode): PincodeInterface
    {
        /** @var Pincode $pincodeModel */
        $pincodeModel = $this->pincodeFactory->create();
        $this->resource->load($pincodeModel, trim($pincode), PincodeInterface::PINCODE);
        if (!$pincodeModel->getId()) {
            throw new NoSuchEntityException(
                __('Postal code mapping for "%1" does not exist.', $pincode)
            );
        }
        return $pincodeModel;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(PincodeInterface $pincode): bool
    {
        try {
            $this->resource->delete($pincode);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete postal code mapping: %1', $exception->getMessage()),
                $exception
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $pincodeId): bool
    {
        return $this->delete($this->getById($pincodeId));
    }
}
