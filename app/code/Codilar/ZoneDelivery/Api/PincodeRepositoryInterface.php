<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api;

use Codilar\ZoneDelivery\Api\Data\PincodeInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface PincodeRepositoryInterface
 * Service contract for managing postal code to zone mappings
 */
interface PincodeRepositoryInterface
{
    /**
     * Save postal code mapping
     *
     * @param PincodeInterface $pincode
     * @return PincodeInterface
     * @throws CouldNotSaveException
     */
    public function save(PincodeInterface $pincode): PincodeInterface;

    /**
     * Get postal code mapping by ID
     *
     * @param int $pincodeId
     * @return PincodeInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $pincodeId): PincodeInterface;

    /**
     * Get postal code mapping by postal code string
     *
     * @param string $pincode
     * @return PincodeInterface
     * @throws NoSuchEntityException
     */
    public function getByPincode(string $pincode): PincodeInterface;

    /**
     * Retrieve postal code mappings matching the search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete postal code mapping
     *
     * @param PincodeInterface $pincode
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(PincodeInterface $pincode): bool;

    /**
     * Delete postal code mapping by ID
     *
     * @param int $pincodeId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $pincodeId): bool;

    /**
     * Resolve Zone ID by 6-digit postal code
     *
     * @param int|string $pincode
     * @return int|null
     */
    public function getZoneIdByPincode($pincode);
}
