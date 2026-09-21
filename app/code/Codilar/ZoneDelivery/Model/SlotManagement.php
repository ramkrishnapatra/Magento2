<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\AvailableSlotInterface;
use Codilar\ZoneDelivery\Api\Data\AvailableSlotInterfaceFactory;
use Codilar\ZoneDelivery\Api\PincodeRepositoryInterface;
use Codilar\ZoneDelivery\Api\SlotManagementInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;

class SlotManagement implements SlotManagementInterface
{
    /**
     * @param ResourceConnection $resourceConnection
     * @param AvailableSlotInterfaceFactory $slotFactory
     * @param TimezoneInterface $timezone
     * @param LoggerInterface $logger
     * @param PincodeRepositoryInterface $pincodeRepository
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private AvailableSlotInterfaceFactory $slotFactory,
        private TimezoneInterface $timezone,
        private LoggerInterface $logger,
        private PincodeRepositoryInterface $pincodeRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getAvailableSlots(string $pincode, string $date): array
    {
        $connection = $this->resourceConnection->getConnection();
        $zoneTable = $this->resourceConnection->getTableName('magecafe_delivery_zone');
        $slotTable = $this->resourceConnection->getTableName('magecafe_delivery_slot');
        $bookingTable = $this->resourceConnection->getTableName('magecafe_delivery_slot_booking');

        $cleanPincode = trim($pincode);
        $cleanDeliveryDate = trim($date);

        if (empty($cleanPincode) || empty($cleanDeliveryDate)) {
            return [];
        }

        // 1. Resolve Zone ID and Zone Reference via Range Pincode Repository
        $zoneId = $this->pincodeRepository->getZoneIdByPincode($cleanPincode);
        if (!$zoneId) {
            $this->logger->info(sprintf('ZoneDelivery: Pincode "%s" not mapped to any serviceable zone.', $cleanPincode));
            return [];
        }

        $selectZoneRef = $connection->select()
            ->from($zoneTable, ['zone_reference'])
            ->where('entity_id = ?', (int)$zoneId);
        $zoneRef = (string)$connection->fetchOne($selectZoneRef);

        // 2. Fetch Active Slots
        $selectSlots = $connection->select()
            ->from($slotTable)
            ->where('is_active = ?', 1)
            ->order('start_time ASC');

        $slots = $connection->fetchAll($selectSlots);
        if (empty($slots)) {
            return [];
        }

        $currentStoreDateTime = $this->timezone->date();
        $currentDate = $currentStoreDateTime->format('Y-m-d');
        $currentTime = $currentStoreDateTime->format('H:i:s');
        $isSameDay = ($cleanDeliveryDate === $currentDate);

        $availableSlots = [];

        foreach ($slots as $slot) {
            $slotId = (int)$slot['slot_id'];
            $rawZones = trim((string)($slot['applicable_zones'] ?? ''));

            // Match against both Zone Reference (e.g. ZONE_REMOTE) and Zone ID (e.g. 3)
            if ($rawZones !== '') {
                $allowedZones = array_map('trim', explode(',', $rawZones));
                $isAllowed = in_array($zoneRef, $allowedZones, true)
                    || in_array((string)$zoneId, $allowedZones, true)
                    || in_array($zoneId, array_map('intval', $allowedZones), true);
                if (!$isAllowed) {
                    continue;
                }
            }

            // Same day cutoff: Do not show past slots for today (BR-02)
            if ($isSameDay) {
                $slotStartTime = (string)$slot['start_time'];
                if ($slotStartTime <= $currentTime) {
                    continue;
                }
            }

            // 3. Count Reserved/Confirmed Bookings against Daily Capacity (BR-01, NFR-01)
            $selectBookings = $connection->select()
                ->from($bookingTable, [new \Zend_Db_Expr('COUNT(*)')])
                ->where('slot_id = ?', $slotId)
                ->where('delivery_date = ?', $cleanDeliveryDate)
                ->where('status IN (?)', ['confirmed', 'reserved']);

            $bookedCount = (int)$connection->fetchOne($selectBookings);
            $totalCapacity = (int)$slot['capacity_per_day'];

            if ($bookedCount >= $totalCapacity) {
                continue;
            }

            /** @var AvailableSlotInterface $slotDto */
            $slotDto = $this->slotFactory->create();
            $slotDto->setSlotId($slotId);
            $slotDto->setSlotReference((string)$slot['slot_reference']);
            $slotDto->setStartTime((string)$slot['start_time']);
            $slotDto->setEndTime((string)$slot['end_time']);
            $slotDto->setIsAvailable(true);

            $availableSlots[] = $slotDto;
        }

        return $availableSlots;
    }
}
