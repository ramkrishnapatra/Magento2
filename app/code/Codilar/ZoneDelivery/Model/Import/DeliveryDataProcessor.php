<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;

class DeliveryDataProcessor
{


    /**
     * @param ResourceConnection $resourceConnection
     * @param Csv $csvReader
     * @param File $fileDriver
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private Csv $csvReader,
        private File $fileDriver,
        private LoggerInterface $logger
    ) {

    }

    /**
     * Process CSV file adhering to BR-07 (All-or-Nothing validation)
     *
     * @param string $filePath
     * @return array
     * @throws LocalizedException
     */
    public function process(string $filePath): array
    {
        if (!$this->fileDriver->isExists($filePath)) {
            throw new LocalizedException(__('The specified file does not exist: %1', $filePath));
        }

        $rows = $this->csvReader->getData($filePath);
        if (empty($rows)) {
            throw new LocalizedException(__('The supplied CSV file is empty.'));
        }

        $headers = array_map('trim', array_shift($rows));
        $headerMap = array_flip($headers);

        $errors = [];
        $validatedData = [
            'zones' => [],
            'pincodes' => [],
            'rates' => [],
            'slots' => []
        ];

        // -------------------------------------------------------------
        // PASS 1: Strict Validation (BR-07 / FR-13 / AC-08)
        // -------------------------------------------------------------
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 accounting for 0-index & header line
            if (empty(array_filter($row))) {
                continue;
            }

            $type = isset($headerMap['type']) ? trim((string)($row[$headerMap['type']] ?? '')) : '';
            if (empty($type)) {
                $errors[] = sprintf('Row %d: Missing mandatory "type" identifier (zone|pincode|rate|slot).', $rowNumber);
                continue;
            }

            switch (strtolower($type)) {
                case 'zone':
                    $zoneRef = trim((string)($row[$headerMap['zone_reference']] ?? ''));
                    $desc = trim((string)($row[$headerMap['description']] ?? ''));
                    $isRemote = (int)($row[$headerMap['is_remote']] ?? 0);

                    if (empty($zoneRef)) {
                        $errors[] = sprintf('Row %d [zone]: "zone_reference" cannot be empty.', $rowNumber);
                    } else {
                        $validatedData['zones'][$zoneRef] = [
                            'zone_reference' => $zoneRef,
                            'description' => $desc,
                            'is_remote' => $isRemote ? 1 : 0
                        ];
                    }
                    break;

                case 'pincode':
                    $pincode = trim((string)($row[$headerMap['pincode']] ?? ''));
                    $targetZone = trim((string)($row[$headerMap['zone_reference']] ?? ''));

                    if (empty($pincode) || strlen($pincode) !== 6 || !ctype_digit($pincode)) {
                        $errors[] = sprintf('Row %d [pincode]: Invalid 6-digit Indian postal code "%s".', $rowNumber, $pincode);
                    } elseif (empty($targetZone)) {
                        $errors[] = sprintf('Row %d [pincode]: Target "zone_reference" is required.', $rowNumber);
                    } else {
                        $validatedData['pincodes'][] = [
                            'pincode' => $pincode,
                            'zone_reference' => $targetZone
                        ];
                    }
                    break;

                case 'rate':
                    $rateZone = trim((string)($row[$headerMap['zone_reference']] ?? ''));
                    $websiteId = (int)($row[$headerMap['website_id']] ?? 0);
                    $weightFrom = (float)($row[$headerMap['weight_from']] ?? 0);
                    $weightTo = (float)($row[$headerMap['weight_to']] ?? 0);
                    $valFrom = (float)($row[$headerMap['order_value_from']] ?? 0);
                    $valTo = (float)($row[$headerMap['order_value_to']] ?? 0);
                    $charge = (float)($row[$headerMap['charge']] ?? 0);
                    $oversized = (float)($row[$headerMap['oversized_surcharge']] ?? 0);

                    if (empty($rateZone)) {
                        $errors[] = sprintf('Row %d [rate]: "zone_reference" is required.', $rowNumber);
                    } elseif ($weightTo < $weightFrom) {
                        $errors[] = sprintf('Row %d [rate]: weight_to (%s) cannot be less than weight_from (%s).', $rowNumber, $weightTo, $weightFrom);
                    } elseif ($charge < 0) {
                        $errors[] = sprintf('Row %d [rate]: charge cannot be negative.', $rowNumber);
                    } else {
                        $validatedData['rates'][] = [
                            'zone_reference' => $rateZone,
                            'website_id' => $websiteId,
                            'weight_from' => $weightFrom,
                            'weight_to' => $weightTo,
                            'order_value_from' => $valFrom,
                            'order_value_to' => $valTo,
                            'charge' => $charge,
                            'oversized_surcharge' => $oversized
                        ];
                    }
                    break;

                case 'slot':
                    $slotRef = trim((string)($row[$headerMap['slot_reference']] ?? ''));
                    $startTime = trim((string)($row[$headerMap['start_time']] ?? ''));
                    $endTime = trim((string)($row[$headerMap['end_time']] ?? ''));
                    $capacity = (int)($row[$headerMap['capacity_per_day']] ?? 0);
                    $applicableZones = trim((string)($row[$headerMap['applicable_zones']] ?? ''));

                    if (empty($slotRef)) {
                        $errors[] = sprintf('Row %d [slot]: "slot_reference" is required.', $rowNumber);
                    } elseif (empty($startTime) || empty($endTime)) {
                        $errors[] = sprintf('Row %d [slot]: "start_time" and "end_time" are mandatory.', $rowNumber);
                    } elseif ($capacity <= 0) {
                        $errors[] = sprintf('Row %d [slot]: "capacity_per_day" must be greater than zero.', $rowNumber);
                    } else {
                        $validatedData['slots'][$slotRef] = [
                            'slot_reference' => $slotRef,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'capacity_per_day' => $capacity,
                            'applicable_zones' => $applicableZones ?: null,
                            'is_active' => 1
                        ];
                    }
                    break;

                default:
                    $errors[] = sprintf('Row %d: Unknown record type "%s". Allowed: zone, pincode, rate, slot.', $rowNumber, $type);
                    break;
            }
        }

        // BR-07: If any errors exist, abort immediately and leave tables untouched
        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // -------------------------------------------------------------
        // PASS 2: Atomic Execution within Database Transaction
        // -------------------------------------------------------------
        $connection = $this->resourceConnection->getConnection();
        $zoneTable = $this->resourceConnection->getTableName('magecafe_delivery_zone');
        $pincodeTable = $this->resourceConnection->getTableName('magecafe_delivery_pincode');
        $rateTable = $this->resourceConnection->getTableName('magecafe_delivery_rate');
        $slotTable = $this->resourceConnection->getTableName('magecafe_delivery_slot');

        $connection->beginTransaction();
        try {
            // 1. Persist Zones
            $zoneIdMap = [];
            foreach ($validatedData['zones'] as $zone) {
                $connection->insertOnDuplicate($zoneTable, $zone, ['description', 'is_remote']);
                $selectId = $connection->select()->from($zoneTable, ['entity_id'])->where('zone_reference = ?', $zone['zone_reference']);
                $zoneIdMap[$zone['zone_reference']] = (int)$connection->fetchOne($selectId);
            }

            // Load existing zones if not all defined in this file
            $existingZones = $connection->fetchPairs($connection->select()->from($zoneTable, ['zone_reference', 'entity_id']));
            foreach ($existingZones as $ref => $id) {
                if (!isset($zoneIdMap[$ref])) {
                    $zoneIdMap[$ref] = (int)$id;
                }
            }

            // 2. Persist Pincodes
            $pincodesCount = 0;
            foreach ($validatedData['pincodes'] as $pinData) {
                $ref = $pinData['zone_reference'];
                if (!isset($zoneIdMap[$ref])) {
                    throw new LocalizedException(__('Referenced zone "%1" does not exist for pincode "%2".', $ref, $pinData['pincode']));
                }
                $connection->insertOnDuplicate(
                    $pincodeTable,
                    [
                        'pincode' => $pinData['pincode'],
                        'zone_id' => $zoneIdMap[$ref]
                    ],
                    ['zone_id']
                );
                $pincodesCount++;
            }

            // 3. Persist Rates
            $ratesCount = 0;
            foreach ($validatedData['rates'] as $rate) {
                $ref = $rate['zone_reference'];
                if (!isset($zoneIdMap[$ref])) {
                    throw new LocalizedException(__('Referenced zone "%1" does not exist for rate tier.', $ref));
                }
                $rate['zone_id'] = $zoneIdMap[$ref];
                unset($rate['zone_reference']);
                $connection->insert($rateTable, $rate);
                $ratesCount++;
            }

            // 4. Persist Delivery Slots
            $slotsCount = 0;
            foreach ($validatedData['slots'] as $slot) {
                $connection->insertOnDuplicate(
                    $slotTable,
                    $slot,
                    ['start_time', 'end_time', 'capacity_per_day', 'applicable_zones', 'is_active']
                );
                $slotsCount++;
            }

            $connection->commit();

            return [
                'errors' => [],
                'zones' => count($validatedData['zones']),
                'pincodes' => $pincodesCount,
                'rates' => $ratesCount,
                'slots' => $slotsCount
            ];
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->error(sprintf('ZoneDelivery Import Transaction Rollback: %s', $e->getMessage()));
            throw new LocalizedException(__('Import transaction rolled back due to error: %1', $e->getMessage()), $e);
        }
    }
}
