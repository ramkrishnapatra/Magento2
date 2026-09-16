<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model\Carrier;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class ZoneDelivery extends AbstractCarrier implements CarrierInterface
{
    /**
     * Carrier unique code identifier
     *
     * @var string
     */
    protected $_code = 'zonedelivery';

    /**
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param ResourceConnection $resourceConnection
     * @param ProductResource $productResource
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        private ResultFactory $rateResultFactory,
        private MethodFactory $rateMethodFactory,
        private ResourceConnection $resourceConnection,
        private ProductResource $productResource,
        private PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect and calculate dynamic zone delivery rates with clear breakdown
     *
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $destPostcode = trim((string)$request->getDestPostcode());
        if (empty($destPostcode)) {
            return false;
        }

        $connection = $this->resourceConnection->getConnection();
        $pincodeTable = $this->resourceConnection->getTableName('magecafe_delivery_pincode');
        $rateTable = $this->resourceConnection->getTableName('magecafe_delivery_rate');

        // 1. Resolve Zone ID from Destination Pincode
        $selectZone = $connection->select()
            ->from($pincodeTable, ['zone_id'])
            ->where('pincode = ?', $destPostcode);

        $zoneId = $connection->fetchOne($selectZone);
        if (!$zoneId) {
            return false;
        }

        $weight = (float)$request->getPackageWeight();
        if ($weight < 0.0) {
            $weight = 0.0;
        }

        $orderValue = (float)$request->getPackageValue();
        if ($orderValue <= 0.0 && $request->getPackagePhysicalValue()) {
            $orderValue = (float)$request->getPackagePhysicalValue();
        }

        // 2. Resolve Rate Tier based on Zone and Total Package Weight
        $selectRate = $connection->select()
            ->from($rateTable, ['charge', 'oversized_surcharge'])
            ->where('zone_id = ?', (int)$zoneId)
            ->where('weight_from <= ?', $weight)
            ->where('weight_to >= ?', $weight)
            ->limit(1);

        $rateData = $connection->fetchRow($selectRate);

        if (!$rateData) {
            $fallbackRateSelect = $connection->select()
                ->from($rateTable, ['charge', 'oversized_surcharge'])
                ->where('zone_id = ?', (int)$zoneId)
                ->order('weight_to DESC')
                ->limit(1);

            $rateData = $connection->fetchRow($fallbackRateSelect);
        }

        if (!$rateData) {
            return false;
        }

        $baseCharge = (float)$rateData['charge'];
        $oversizedSurchargeRate = (float)$rateData['oversized_surcharge'];

        // 3. Free Shipping Threshold Logic
        $freeShippingThreshold = $this->getConfigData('free_shipping_threshold');
        $isFreeShipping = false;

        if ($freeShippingThreshold !== null && trim((string)$freeShippingThreshold) !== '') {
            $threshold = (float)$freeShippingThreshold;
            if ($threshold > 0 && $orderValue >= $threshold) {
                $baseCharge = 0.0;
                $isFreeShipping = true;
            }
        }

        // 4. Oversized Items Check & Quantity Aggregation
        $oversizedItemsCount = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                $product = $item->getProduct();
                $isOversized = false;

                if ($product) {
                    $val = $product->getData('is_oversized');

                    if ($val === null && $product->getId()) {
                        $rawVal = $this->productResource->getAttributeRawValue(
                            (int)$product->getId(),
                            'is_oversized',
                            (int)$request->getStoreId()
                        );

                        if (is_array($rawVal)) {
                            $val = reset($rawVal);
                        } else {
                            $val = $rawVal;
                        }
                    }

                    if (is_array($val)) {
                        $val = reset($val);
                    }

                    $isOversized = ((string)$val === '1' || $val === true || $val === 1);
                }

                if ($isOversized) {
                    $oversizedItemsCount += (int)$item->getQty();
                }
            }
        }

        // 5. Compute Final Pricing and Transparent Title
        $totalSurcharge = 0.0;
        if ($oversizedItemsCount > 0 && $oversizedSurchargeRate > 0) {
            $totalSurcharge = $oversizedSurchargeRate * $oversizedItemsCount;
        }

        $finalShippingPrice = $baseCharge + $totalSurcharge;

        /** @var Result $result */
        $result = $this->rateResultFactory->create();

        /** @var Method $method */
        $method = $this->rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title') ?: 'Zone Delivery Service');
        $method->setMethod('standard');

        $baseTitle = (string)($this->getConfigData('name') ?: 'Standard Zone Delivery');
        $formattedBaseCharge = $this->priceCurrency->format($baseCharge, false);
        $formattedSurcharge = $this->priceCurrency->format($totalSurcharge, false);

        if ($isFreeShipping) {
            if ($totalSurcharge > 0.0) {
                $methodTitle = sprintf(
                    '%s (Free Base Delivery + %s Oversized Surcharge)',
                    $baseTitle,
                    $formattedSurcharge
                );
            } else {
                $methodTitle = sprintf('%s (Free Base Delivery)', $baseTitle);
            }
        } elseif ($totalSurcharge > 0.0) {
            $methodTitle = sprintf(
                '%s (Base Charge: %s + %s Oversized Surcharge)',
                $baseTitle,
                $formattedBaseCharge,
                $formattedSurcharge
            );
        } else {
            $methodTitle = sprintf(
                '%s (Base Charge: %s)',
                $baseTitle,
                $formattedBaseCharge
            );
        }

        $method->setMethodTitle($methodTitle);
        $method->setPrice($finalShippingPrice);
        $method->setCost($finalShippingPrice);

        $result->append($method);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name') ?: 'Standard Zone Delivery'];
    }
}
