<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\DateManagementInterface;
use Codilar\ZoneDelivery\Api\PincodeRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class DateManagement implements DateManagementInterface
{
    private const XML_PATH_CUTOFF_TIME = 'carriers/zonedelivery/cutoff_time';
    private const DEFAULT_CUTOFF_TIME = '14:00:00';
    private const DATE_WINDOW_DAYS = 7;

    public function __construct(
        private ResourceConnection $resourceConnection,
        private TimezoneInterface $timezone,
        private ScopeConfigInterface $scopeConfig,
        private PincodeRepositoryInterface $pincodeRepository,
        private LoggerInterface $logger
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
        $numericPincode = (int)$cleanPincode;

        // Resolve Zone and Remote status safely with numeric casting for varchar/int safety
        $select = $connection->select()
            ->from(['p' => $pincodeTable], [])
            ->joinInner(['z' => $zoneTable], 'p.zone_id = z.entity_id', ['is_remote'])
            ->where('? BETWEEN CAST(p.pincode_from AS UNSIGNED) AND CAST(p.pincode_to AS UNSIGNED)', $numericPincode)
            ->limit(1);

        $zone = $connection->fetchRow($select);
        if (!$zone) {
            $this->logger->info(sprintf('ZoneDelivery: No range match found for pincode %s', $cleanPincode));
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

        // Remote zone: strict 2 days later. Standard zone: Day + 1 before cutoff, Day + 2 past cutoff
        if ($isRemote) {
            $leadDays = 2;
        } else {
            $leadDays = ($currentTime > $cutoffTime) ? 2 : 1;
        }

        $this->logger->info(sprintf(
            'ZoneDelivery Dates -> Pincode: %s | isRemote: %d | leadDays: %d',
            $cleanPincode,
            $isRemote ? 1 : 0,
            $leadDays
        ));

        $availableDates = [];
        $currentTimestamp = $storeDateTime->getTimestamp();

        for ($i = $leadDays; count($availableDates) < self::DATE_WINDOW_DAYS; $i++) {
            $targetTimestamp = strtotime(sprintf('+%d days', $i), $currentTimestamp);
            // Skip Sunday (day of week 7)
            $dayOfWeek = (int)date('N', $targetTimestamp);
            if ($dayOfWeek === 7) {
                continue;
            }
            $availableDates[] = date('Y-m-d', $targetTimestamp);
        }

        return $availableDates;
    }
}
