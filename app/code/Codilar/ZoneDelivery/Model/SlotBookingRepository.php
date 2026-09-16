<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\SlotBookingInterface;
use Codilar\ZoneDelivery\Api\SlotBookingRepositoryInterface;
use Codilar\ZoneDelivery\Api\SlotRepositoryInterface;
use Codilar\ZoneDelivery\Model\ResourceModel\SlotBooking as SlotBookingResource;
use Codilar\ZoneDelivery\Model\ResourceModel\SlotBooking\CollectionFactory as SlotBookingCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class SlotBookingRepository
 * Implementation of SlotBookingRepositoryInterface handling pessimistic concurrency and booking lifecycle
 */
class SlotBookingRepository implements SlotBookingRepositoryInterface
{

    /**
     * @param SlotBookingResource $resource
     * @param SlotBookingFactory $bookingFactory
     * @param SlotBookingCollectionFactory $collectionFactory
     * @param SlotRepositoryInterface $slotRepository
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        private SlotBookingResource $resource,
        private SlotBookingFactory $bookingFactory,
        private SlotBookingCollectionFactory $collectionFactory,
        private SlotRepositoryInterface $slotRepository,
        private SearchResultsInterfaceFactory $searchResultsFactory,
        private CollectionProcessorInterface $collectionProcessor,
        private DateTime $dateTime,
        private LoggerInterface $logger
    ) {

    }

    /**
     * @inheritdoc
     */
    public function save(SlotBookingInterface $booking): SlotBookingInterface
    {
        try {
            $this->resource->save($booking);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save delivery slot booking: %1', $exception->getMessage()),
                $exception
            );
        }
        return $booking;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $bookingId): SlotBookingInterface
    {
        /** @var SlotBooking $booking */
        $booking = $this->bookingFactory->create();
        $this->resource->load($booking, $bookingId);
        if (!$booking->getId()) {
            throw new NoSuchEntityException(
                __('Delivery slot booking with ID "%1" does not exist.', $bookingId)
            );
        }
        return $booking;
    }

    /**
     * @inheritdoc
     */
    public function getActiveBookingByQuoteId(int $quoteId): ?SlotBookingInterface
    {
        $data = $this->resource->getActiveBookingByQuoteId($quoteId);
        if (!$data || empty($data['booking_id'])) {
            return null;
        }

        /** @var SlotBooking $booking */
        $booking = $this->bookingFactory->create();
        $this->resource->load($booking, (int)$data['booking_id']);
        return $booking->getId() ? $booking : null;
    }

    /**
     * @inheritdoc
     */
    public function reserveSlot(int $slotId, string $deliveryDate, int $quoteId): SlotBookingInterface
    {
        $slot = $this->slotRepository->getById($slotId);
        $capacity = (int)$slot->getCapacityPerDay();
        $connection = $this->resource->getConnection();

        $connection->beginTransaction();
        try {
            // Cancel previous active hold for this quote if customer switches slot/date
            $this->resource->releaseQuoteReservation($quoteId);

            // Pessimistic locking: read active bookings with row lock
            $activeCount = $this->resource->getActiveBookingCount($slotId, $deliveryDate, true);

            if ($activeCount >= $capacity) {
                throw new LocalizedException(
                    __('The selected delivery slot is fully booked for date %1. Please choose another slot.', $deliveryDate)
                );
            }

            /** @var SlotBooking $booking */
            $booking = $this->bookingFactory->create();
            $booking->setSlotId($slotId);
            $booking->setDeliveryDate($deliveryDate);
            $booking->setQuoteId($quoteId);
            $booking->setStatus(SlotBookingInterface::STATUS_RESERVED);
            $booking->setReservedAt($this->dateTime->gmtDate());

            $this->resource->save($booking);
            $connection->commit();

            return $booking;
        } catch (\Exception $e) {
            $connection->rollBack();
            if ($e instanceof LocalizedException) {
                throw $e;
            }
            throw new CouldNotSaveException(
                __('Unable to reserve delivery slot: %1', $e->getMessage()),
                $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function confirmBooking(int $quoteId, int $orderId): bool
    {
        $connection = $this->resource->getConnection();
        $bookingTable = $this->resource->getMainTable();
        $quoteTable = $this->resource->getTable('quote');
        $slotTable = $this->resource->getTable('magecafe_delivery_slot');

        $connection->beginTransaction();
        try {
            // 1. Fetch delivery slot information from quote
            $selectQuote = $connection->select()
                ->from($quoteTable, ['delivery_slot_id', 'delivery_date'])
                ->where('entity_id = ?', $quoteId);

            $quoteData = $connection->fetchRow($selectQuote);
            if (!$quoteData || empty($quoteData['delivery_slot_id']) || empty($quoteData['delivery_date'])) {
                $connection->rollBack();
                return false;
            }

            $slotId = (int)$quoteData['delivery_slot_id'];
            $deliveryDate = (string)$quoteData['delivery_date'];

            // 2. Pessimistic Row Lock on Slot Capacity (NFR-01 / BR-01)
            $selectSlot = $connection->select()
                ->from($slotTable, ['capacity_per_day', 'is_active'])
                ->where('slot_id = ?', $slotId)
                ->forUpdate();

            $slot = $connection->fetchRow($selectSlot);
            if (!$slot || (int)$slot['is_active'] !== 1) {
                throw new LocalizedException(__('The selected delivery slot is no longer available.'));
            }

            $capacity = (int)$slot['capacity_per_day'];

            // 3. Count confirmed bookings for this slot and date
            $countSelect = $connection->select()
                ->from($bookingTable, [new \Zend_Db_Expr('COUNT(*)')])
                ->where('slot_id = ?', $slotId)
                ->where('delivery_date = ?', $deliveryDate)
                ->where('status = ?', SlotBookingInterface::STATUS_CONFIRMED);

            $confirmedBookings = (int)$connection->fetchOne($countSelect);

            if ($confirmedBookings >= $capacity) {
                throw new LocalizedException(
                    __('Selected delivery slot reached maximum daily capacity.')
                );
            }

            // 4. Update existing reservation or insert new confirmed record
            $currentTime = $this->dateTime->gmtDate();
            $existingReservationSelect = $connection->select()
                ->from($bookingTable, ['booking_id'])
                ->where('quote_id = ?', $quoteId)
                ->where('slot_id = ?', $slotId)
                ->where('delivery_date = ?', $deliveryDate)
                ->limit(1);

            $existingBookingId = $connection->fetchOne($existingReservationSelect);

            if ($existingBookingId) {
                $connection->update(
                    $bookingTable,
                    [
                        'order_id' => $orderId,
                        'status' => SlotBookingInterface::STATUS_CONFIRMED,
                        'confirmed_at' => $currentTime
                    ],
                    ['booking_id = ?' => (int)$existingBookingId]
                );
            } else {
                $connection->insert(
                    $bookingTable,
                    [
                        'slot_id' => $slotId,
                        'delivery_date' => $deliveryDate,
                        'quote_id' => $quoteId,
                        'order_id' => $orderId,
                        'status' => SlotBookingInterface::STATUS_CONFIRMED,
                        'reserved_at' => $currentTime,
                        'confirmed_at' => $currentTime
                    ]
                );
            }

            $connection->commit();
            return true;
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->error(sprintf(
                'ZoneDelivery Booking Confirmation Error (Quote ID %s, Order ID %s): %s',
                $quoteId,
                $orderId,
                $e->getMessage()
            ));
            throw new LocalizedException(__($e->getMessage()));
        }
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
    public function delete(SlotBookingInterface $booking): bool
    {
        try {
            $this->resource->delete($booking);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete delivery slot booking: %1', $exception->getMessage()),
                $exception
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $bookingId): bool
    {
        return $this->delete($this->getById($bookingId));
    }
}
