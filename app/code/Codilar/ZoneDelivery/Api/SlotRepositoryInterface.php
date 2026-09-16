<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api;

use Codilar\ZoneDelivery\Api\Data\SlotInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface SlotRepositoryInterface
 * Service contract for managing delivery time slot entities
 */
interface SlotRepositoryInterface
{
    /**
     * Save delivery slot
     *
     * @param SlotInterface $slot
     * @return SlotInterface
     * @throws CouldNotSaveException
     */
    public function save(SlotInterface $slot): SlotInterface;

    /**
     * Get delivery slot by ID
     *
     * @param int $slotId
     * @return SlotInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $slotId): SlotInterface;

    /**
     * Get delivery slot by reference code
     *
     * @param string $slotReference
     * @return SlotInterface
     * @throws NoSuchEntityException
     */
    public function getByReference(string $slotReference): SlotInterface;

    /**
     * Retrieve delivery slots applicable for a specific zone
     *
     * @param int $zoneId
     * @return SlotInterface[]
     */
    public function getAvailableSlotsByZone(int $zoneId): array;

    /**
     * Retrieve delivery slots matching search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete delivery slot
     *
     * @param SlotInterface $slot
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(SlotInterface $slot): bool;

    /**
     * Delete delivery slot by ID
     *
     * @param int $slotId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $slotId): bool;
}
