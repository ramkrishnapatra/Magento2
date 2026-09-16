<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api;

use Codilar\ZoneDelivery\Api\Data\AvailableSlotInterface;

/**
 * Service contract for retrieving available delivery time slots
 * @api
 */
interface SlotManagementInterface
{
    /**
     * Retrieve available slots for a destination pincode and delivery date
     *
     * @param string $pincode
     * @param string $date
     * @return \Codilar\ZoneDelivery\Api\Data\AvailableSlotInterface[]
     */
    public function getAvailableSlots(string $pincode, string $date): array;
}
