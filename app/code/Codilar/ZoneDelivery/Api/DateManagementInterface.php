<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Api;

/**
 * Interface DateManagementInterface
 * Service contract for calculating valid delivery date windows according to BRD-04
 */
interface DateManagementInterface
{
    /**
     * Retrieve list of authorized delivery dates (YYYY-MM-DD) for destination postal code
     *
     * @param string $pincode
     * @return string[]
     */
    public function getAvailableDates(string $pincode): array;
}
