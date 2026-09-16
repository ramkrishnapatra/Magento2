<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\DateManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;

class DateManagement implements DateManagementInterface
{
    private const XML_PATH_CUTOFF_TIME = 'carriers/zonedelivery/cutoff_time';
    private const DEFAULT_CUTOFF_TIME = '14:00:00';
    private const DATE_WINDOW_DAYS = 7;



    /**
     * @param ResourceConnection $resourceConnection
     * @param TimezoneInterface $timezone
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private TimezoneInterface $timezone,
        private ScopeConfigInterface $scopeConfig
    ) {

    }

    /**
     * @inheritDoc
     */
    public function getAvailableDates(string $pincode): array
    {
        $cleanPincode = trim($pincode);
        if (empty($cleanPincode)) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $pincodeTable = $this->resourceConnection->getTableName('magecafe_delivery_pincode');
        $zoneTable = $this->resourceConnection->getTableName('magecafe_delivery_zone');

        $select = $connection->select()
            ->from(['p' => $pincodeTable], [])
            ->joinInner(['z' => $zoneTable], 'p.zone_id = z.entity_id', ['is_remote'])
            ->where('p.pincode = ?', $cleanPincode);

        $zone = $connection->fetchRow($select);
        if (!$zone) {
            return [];
        }

        $isRemote = (bool)(int)$zone['is_remote'];

        $storeDateTime = $this->timezone->date();
        $currentTime = $storeDateTime->format('H:i:s');
        $cutoffConfig = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CUTOFF_TIME,
            ScopeInterface::SCOPE_STORE
        );
        $cutoffTime = !empty($cutoffConfig) ? $cutoffConfig : self::DEFAULT_CUTOFF_TIME;

        // Determine initial lead time in days
        if ($isRemote) {
            // BR-02: Next-day not available for remote. Earliest is Day + 2 (or Day + 3 if past cutoff)
            $leadDays = ($currentTime > $cutoffTime) ? 3 : 2;
        } else {
            // Standard zone: Day + 1 if before cutoff, Day + 2 if past cutoff
            $leadDays = ($currentTime > $cutoffTime) ? 2 : 1;
        }

        $availableDates = [];
        $currentTimestamp = $storeDateTime->getTimestamp();

        for ($i = $leadDays; count($availableDates) < self::DATE_WINDOW_DAYS; $i++) {
            $targetTimestamp = strtotime(sprintf('+%d days', $i), $currentTimestamp);
            // Skip Sunday if deliveries are closed, otherwise add
            $dayOfWeek = (int)date('N', $targetTimestamp); // 1 (Mon) to 7 (Sun)
            if ($dayOfWeek === 7) {
                continue;
            }
            $availableDates[] = date('Y-m-d', $targetTimestamp);
        }

        return $availableDates;
    }
}
