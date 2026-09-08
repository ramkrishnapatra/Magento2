<?php
declare(strict_types=1);

namespace Codilar\CustomerStats\Block;

use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Stats extends Template
{
    private CustomerSessionFactory $customerSessionFactory;
    private OrderCollectionFactory $orderCollectionFactory;
    private PriceCurrencyInterface $priceCurrency;

    public function __construct(
        Context $context,
        CustomerSessionFactory $customerSessionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSessionFactory = $customerSessionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->priceCurrency = $priceCurrency;

        // Disables Block HTML caching for this block
        $this->addData(['cache_lifetime' => null]);
    }

    public function getCustomerOrderStats(): array
    {
        $customerId = $this->customerSessionFactory->create()->getCustomerId();
        if (!$customerId) {
            return [];
        }

        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', (int)$customerId)
            ->setOrder('created_at', 'DESC');

        $totalOrders = $orders->getSize();
        $totalSpent = 0.0;
        $completedOrders = 0;
        $canceledOrders = 0;
        $lastOrderIncrementId = 'N/A';

        if ($totalOrders > 0) {
            $firstItem = $orders->getFirstItem();
            $lastOrderIncrementId = '#' . $firstItem->getIncrementId();
        }

        foreach ($orders as $order) {
            $totalSpent += (float)$order->getBaseGrandTotal();

            if ($order->getStatus() === Order::STATE_COMPLETE) {
                $completedOrders++;
            } elseif ($order->getStatus() === Order::STATE_CANCELED) {
                $canceledOrders++;
            }
        }

        return [
            'total_orders'     => $totalOrders,
            'total_spent'      => $this->priceCurrency->format($totalSpent, false),
            'last_order'       => $lastOrderIncrementId,
            'completed_orders' => $completedOrders,
            'canceled_orders'  => $canceledOrders,
        ];
    }
}
