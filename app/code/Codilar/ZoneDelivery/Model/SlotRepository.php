<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\SlotInterface;
use Codilar\ZoneDelivery\Api\SlotRepositoryInterface;
use Codilar\ZoneDelivery\Model\ResourceModel\Slot as SlotResource;
use Codilar\ZoneDelivery\Model\ResourceModel\Slot\CollectionFactory as SlotCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SlotRepository
 * Implementation of SlotRepositoryInterface handling delivery slot persistence
 */
class SlotRepository implements SlotRepositoryInterface
{

    /**
     * @param SlotResource $resource
     * @param SlotFactory $slotFactory
     * @param SlotCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private SlotResource $resource,
        private SlotFactory $slotFactory,
        private SlotCollectionFactory $collectionFactory,
        private SearchResultsInterfaceFactory $searchResultsFactory,
        private CollectionProcessorInterface $collectionProcessor
    ) {

    }

    /**
     * @inheritdoc
     */
    public function save(SlotInterface $slot): SlotInterface
    {
        try {
            $this->resource->save($slot);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save delivery slot: %1', $exception->getMessage()),
                $exception
            );
        }
        return $slot;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $slotId): SlotInterface
    {
        /** @var Slot $slot */
        $slot = $this->slotFactory->create();
        $this->resource->load($slot, $slotId);
        if (!$slot->getId()) {
            throw new NoSuchEntityException(
                __('Delivery slot with ID "%1" does not exist.', $slotId)
            );
        }
        return $slot;
    }

    /**
     * @inheritdoc
     */
    public function getByReference(string $slotReference): SlotInterface
    {
        /** @var Slot $slot */
        $slot = $this->slotFactory->create();
        $this->resource->load($slot, $slotReference, SlotInterface::SLOT_REFERENCE);
        if (!$slot->getId()) {
            throw new NoSuchEntityException(
                __('Delivery slot with reference "%1" does not exist.', $slotReference)
            );
        }
        return $slot;
    }

    /**
     * @inheritdoc
     */
    public function getAvailableSlotsByZone(int $zoneId): array
    {
        $rawSlots = $this->resource->getSlotsByZone($zoneId);
        $slots = [];

        foreach ($rawSlots as $data) {
            /** @var Slot $slot */
            $slot = $this->slotFactory->create();
            $slot->setData($data);
            $slots[] = $slot;
        }

        return $slots;
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
    public function delete(SlotInterface $slot): bool
    {
        try {
            $this->resource->delete($slot);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete delivery slot: %1', $exception->getMessage()),
                $exception
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $slotId): bool
    {
        return $this->delete($this->getById($slotId));
    }
}
