<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Block\Order;

use Codilar\ZoneDelivery\Api\SlotRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class DeliverySlotInfo extends Template
{


    /**
     * @param Context $context
     * @param Registry $registry
     * @param SlotRepositoryInterface $slotRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        private Registry $registry,
        private SlotRepositoryInterface $slotRepository,
        private OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

    }

    /**
     * Resolve Order instance across Customer View, Admin View, and Transactional Emails
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        // 1. Check direct block property or data array (e.g. in emails)
        $order = $this->getData('order');
        if ($order instanceof Order) {
            return $order;
        }

        if ($this->hasData('order_id')) {
            try {
                $loadedOrder = $this->orderRepository->get((int)$this->getData('order_id'));
                if ($loadedOrder instanceof Order) {
                    return $loadedOrder;
                }
            } catch (\Exception $e) {
                // Fallback silently
            }
        }

        // 2. Check parent block getOrder()
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && method_exists($parentBlock, 'getOrder')) {
            $parentOrder = $parentBlock->getOrder();
            if ($parentOrder instanceof Order) {
                return $parentOrder;
            }
        }

        // 3. Check Storefront Account or Admin registry
        $currentOrder = $this->registry->registry('current_order');
        if ($currentOrder instanceof Order) {
            return $currentOrder;
        }

        return null;
    }

    /**
     * Retrieve formatted slot reference description with start and end times
     *
     * @param int $slotId
     * @return string
     */
    public function getSlotReference(int $slotId): string
    {
        if ($slotId <= 0) {
            return '';
        }

        try {
            $slot = $this->slotRepository->getById($slotId);
            return sprintf(
                '%s (%s - %s)',
                $slot->getSlotReference(),
                date('h:i A', strtotime($slot->getStartTime())),
                date('h:i A', strtotime($slot->getEndTime()))
            );
        } catch (NoSuchEntityException $e) {
            return (string)$slotId;
        }
    }
}
