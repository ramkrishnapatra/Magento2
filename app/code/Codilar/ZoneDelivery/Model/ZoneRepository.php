<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\ZoneInterface;
use Codilar\ZoneDelivery\Api\ZoneRepositoryInterface;
use Codilar\ZoneDelivery\Model\ResourceModel\Zone as ZoneResource;
use Codilar\ZoneDelivery\Model\ResourceModel\Zone\CollectionFactory as ZoneCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ZoneRepository
 * Implementation of ZoneRepositoryInterface handling persistence of delivery zone entities
 */
class ZoneRepository implements ZoneRepositoryInterface
{

    /**
     * @param ZoneResource $resource
     * @param ZoneFactory $zoneFactory
     * @param ZoneCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private ZoneResource $resource,
        private ZoneFactory $zoneFactory,
        private ZoneCollectionFactory $collectionFactory,
        private SearchResultsInterfaceFactory $searchResultsFactory,
        private CollectionProcessorInterface $collectionProcessor
    ) {

    }

    /**
     * @inheritdoc
     */
    public function save(ZoneInterface $zone): ZoneInterface
    {
        try {
            $this->resource->save($zone);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save delivery zone: %1', $exception->getMessage()),
                $exception
            );
        }
        return $zone;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $zoneId): ZoneInterface
    {
        /** @var Zone $zone */
        $zone = $this->zoneFactory->create();
        $this->resource->load($zone, $zoneId);
        if (!$zone->getId()) {
            throw new NoSuchEntityException(__('Delivery zone with ID "%1" does not exist.', $zoneId));
        }
        return $zone;
    }

    /**
     * @inheritdoc
     */
    public function getByReference(string $zoneReference): ZoneInterface
    {
        /** @var Zone $zone */
        $zone = $this->zoneFactory->create();
        $this->resource->load($zone, $zoneReference, ZoneInterface::ZONE_REFERENCE);
        if (!$zone->getId()) {
            throw new NoSuchEntityException(
                __('Delivery zone with reference "%1" does not exist.', $zoneReference)
            );
        }
        return $zone;
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
    public function delete(ZoneInterface $zone): bool
    {
        try {
            $this->resource->delete($zone);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete delivery zone: %1', $exception->getMessage()),
                $exception
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $zoneId): bool
    {
        return $this->delete($this->getById($zoneId));
    }
}
