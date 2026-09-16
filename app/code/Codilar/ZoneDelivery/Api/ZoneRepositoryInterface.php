<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api;

use Codilar\ZoneDelivery\Api\Data\ZoneInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ZoneRepositoryInterface
 * Service contract for managing delivery zone entities
 */
interface ZoneRepositoryInterface
{
    /**
     * Save delivery zone
     *
     * @param ZoneInterface $zone
     * @return ZoneInterface
     * @throws CouldNotSaveException
     */
    public function save(ZoneInterface $zone): ZoneInterface;

    /**
     * Get delivery zone by ID
     *
     * @param int $zoneId
     * @return ZoneInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $zoneId): ZoneInterface;

    /**
     * Get delivery zone by reference code
     *
     * @param string $zoneReference
     * @return ZoneInterface
     * @throws NoSuchEntityException
     */
    public function getByReference(string $zoneReference): ZoneInterface;

    /**
     * Retrieve delivery zones matching the search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete delivery zone
     *
     * @param ZoneInterface $zone
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ZoneInterface $zone): bool;

    /**
     * Delete delivery zone by ID
     *
     * @param int $zoneId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $zoneId): bool;
}
