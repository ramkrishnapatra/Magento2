<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api;

use Codilar\ZoneDelivery\Api\Data\RateInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface RateRepositoryInterface
 * Service contract for managing delivery rate tier entities
 */
interface RateRepositoryInterface
{
    /**
     * Save delivery rate tier
     *
     * @param RateInterface $rate
     * @return RateInterface
     * @throws CouldNotSaveException
     */
    public function save(RateInterface $rate): RateInterface;

    /**
     * Get delivery rate tier by ID
     *
     * @param int $rateId
     * @return RateInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $rateId): RateInterface;

    /**
     * Resolve matching delivery rate for carrier calculation
     *
     * @param int $zoneId
     * @param int $websiteId
     * @param float $weight
     * @param float $orderValue
     * @return RateInterface|null
     */
    public function getMatchingRate(
        int $zoneId,
        int $websiteId,
        float $weight,
        float $orderValue
    ): ?RateInterface;

    /**
     * Retrieve delivery rate tiers matching search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete delivery rate tier
     *
     * @param RateInterface $rate
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(RateInterface $rate): bool;

    /**
     * Delete delivery rate tier by ID
     *
     * @param int $rateId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $rateId): bool;
}
