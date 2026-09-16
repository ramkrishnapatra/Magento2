<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api;

use Codilar\ZoneDelivery\Api\Data\SlotBookingInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface SlotBookingRepositoryInterface
 * Service contract for managing delivery slot booking reservations
 */
interface SlotBookingRepositoryInterface
{
    /**
     * Save slot booking entity
     *
     * @param SlotBookingInterface $booking
     * @return SlotBookingInterface
     * @throws CouldNotSaveException
     */
    public function save(SlotBookingInterface $booking): SlotBookingInterface;

    /**
     * Retrieve slot booking by ID
     *
     * @param int $bookingId
     * @return SlotBookingInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $bookingId): SlotBookingInterface;

    /**
     * Retrieve active reserved booking for a quote
     *
     * @param int $quoteId
     * @return SlotBookingInterface|null
     */
    public function getActiveBookingByQuoteId(int $quoteId): ?SlotBookingInterface;

    /**
     * Reserve a delivery slot under pessimistic lock
     *
     * @param int $slotId
     * @param string $deliveryDate (YYYY-MM-DD)
     * @param int $quoteId
     * @return SlotBookingInterface
     * @throws LocalizedException If slot capacity is exceeded
     * @throws CouldNotSaveException
     */
    public function reserveSlot(int $slotId, string $deliveryDate, int $quoteId): SlotBookingInterface;

    /**
     * Confirm booking upon order placement
     *
     * @param int $quoteId
     * @param int $orderId
     * @return bool
     * @throws CouldNotSaveException
     */
    public function confirmBooking(int $quoteId, int $orderId): bool;

    /**
     * Retrieve bookings matching search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete slot booking entity
     *
     * @param SlotBookingInterface $booking
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(SlotBookingInterface $booking): bool;

    /**
     * Delete slot booking by ID
     *
     * @param int $bookingId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $bookingId): bool;
}
