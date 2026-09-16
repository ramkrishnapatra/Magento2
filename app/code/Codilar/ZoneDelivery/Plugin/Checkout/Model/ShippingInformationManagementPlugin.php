<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class ShippingInformationManagementPlugin
{

    /**
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private QuoteRepository $quoteRepository,
        private LoggerInterface $logger
    ) {

    }

    /**
     * Intercept and persist delivery slot attributes before saving address information
     *
     * @param ShippingInformationManagementInterface $subject
     * @param int|string $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     * @throws LocalizedException
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ): array {
        $slotId = null;
        $deliveryDate = null;

        // 1. Safe extraction from Root Shipping Information Extension Attributes
        $extAttributes = $addressInformation->getExtensionAttributes();
        if ($extAttributes) {
            if (method_exists($extAttributes, 'getDeliverySlotId')) {
                $slotId = $extAttributes->getDeliverySlotId();
            }
            if (method_exists($extAttributes, 'getDeliveryDate')) {
                $deliveryDate = $extAttributes->getDeliveryDate();
            }
        }

        // 2. Safe Fallback: Check Shipping Address Extension Attributes
        if ((!$slotId || !$deliveryDate) && $addressInformation->getShippingAddress()) {
            $addressExt = $addressInformation->getShippingAddress()->getExtensionAttributes();
            if ($addressExt) {
                if (!$slotId && method_exists($addressExt, 'getDeliverySlotId')) {
                    $slotId = $addressExt->getDeliverySlotId();
                }
                if (!$deliveryDate && method_exists($addressExt, 'getDeliveryDate')) {
                    $deliveryDate = $addressExt->getDeliveryDate();
                }
            }
        }

        $carrierCode = $addressInformation->getShippingCarrierCode();

        // Enforce validation only when Zone Delivery shipping method is chosen
        if ($carrierCode === 'zonedelivery') {
            if (empty($slotId) || empty($deliveryDate)) {
                throw new LocalizedException(
                    __('Please select both a delivery date and a valid delivery slot.')
                );
            }
        }

        // 3. Persist data to Active Quote safely
        if (!empty($slotId) || !empty($deliveryDate)) {
            try {
                $quote = $this->quoteRepository->getActive((int)$cartId);

                if (!empty($slotId)) {
                    $quote->setData('delivery_slot_id', (int)$slotId);
                }
                if (!empty($deliveryDate)) {
                    $quote->setData('delivery_date', (string)$deliveryDate);
                }

                $this->quoteRepository->save($quote);
            } catch (LocalizedException $e) {
                throw $e;
            } catch (\Exception $e) {
                $this->logger->error(
                    sprintf('ZoneDelivery: Persistence failure on Quote %s: %s', $cartId, $e->getMessage())
                );
                throw new LocalizedException(
                    __('Unable to save delivery slot information to cart. Please try again.')
                );
            }
        }

        return [$cartId, $addressInformation];
    }
}
