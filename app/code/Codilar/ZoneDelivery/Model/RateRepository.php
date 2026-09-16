<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\RateInterface;
use Codilar\ZoneDelivery\Api\RateRepositoryInterface;
use Codilar\ZoneDelivery\Model\ResourceModel\Rate as RateResource;
use Codilar\ZoneDelivery\Model\ResourceModel\Rate\CollectionFactory as RateCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class RateRepository
 * Implementation of RateRepositoryInterface handling delivery rate persistence
 */
class RateRepository implements RateRepositoryInterface
{


    /**
     * @param RateResource $resource
     * @param RateFactory $rateFactory
     * @param RateCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private RateResource $resource,
        private RateFactory $rateFactory,
        private RateCollectionFactory $collectionFactory,
        private SearchResultsInterfaceFactory $searchResultsFactory,
        private CollectionProcessorInterface $collectionProcessor
    ) {

    }

    /**
     * @inheritdoc
     */
    public function save(RateInterface $rate): RateInterface
    {
        try {
            $this->resource->save($rate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save delivery rate: %1', $exception->getMessage()),
                $exception
            );
        }
        return $rate;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $rateId): RateInterface
    {
        /** @var Rate $rate */
        $rate = $this->rateFactory->create();
        $this->resource->load($rate, $rateId);
        if (!$rate->getId()) {
            throw new NoSuchEntityException(
                __('Delivery rate with ID "%1" does not exist.', $rateId)
            );
        }
        return $rate;
    }

    /**
     * @inheritdoc
     */
    public function getMatchingRate(
        int $zoneId,
        int $websiteId,
        float $weight,
        float $orderValue
    ): ?RateInterface {
        $rateData = $this->resource->getMatchingRate($zoneId, $websiteId, $weight, $orderValue);
        if (!$rateData) {
            return null;
        }

        /** @var Rate $rate */
        $rate = $this->rateFactory->create();
        $rate->setData($rateData);
        return $rate;
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
    public function delete(RateInterface $rate): bool
    {
        try {
            $this->resource->delete($rate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete delivery rate: %1', $exception->getMessage()),
                $exception
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $rateId): bool
    {
        return $this->delete($this->getById($rateId));
    }
}
