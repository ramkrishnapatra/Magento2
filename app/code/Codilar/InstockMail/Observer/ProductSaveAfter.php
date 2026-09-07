<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Observer;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Codilar\InstockMail\Api\StockAlertRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ProductSaveAfter implements ObserverInterface
{
    protected TransportBuilder $transportBuilder;
    protected ScopeConfigInterface $scopeConfig;
    protected StoreManagerInterface $storeManager;
    protected Emulation $emulation;
    protected StockAlertRepositoryInterface $stockAlertRepository;
    protected CustomerRepositoryInterface $customerRepository;
    protected DateTime $dateTime;
    protected LoggerInterface $logger;

    public function __construct(
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Emulation $emulation,
        StockAlertRepositoryInterface $stockAlertRepository,
        CustomerRepositoryInterface $customerRepository,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->emulation = $emulation;
        $this->stockAlertRepository = $stockAlertRepository;
        $this->customerRepository = $customerRepository;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        try {
            /** @var Product $product */
            $product = $observer->getEvent()->getProduct();
            if (!$product || !$product->getId()) {
                return;
            }

            $productId = (int)$product->getId();

            $stockData = $product->getQuantityAndStockStatus();
            $isInStock = false;

            if (is_array($stockData) && isset($stockData['is_in_stock'])) {
                $isInStock = (bool)$stockData['is_in_stock'];
            } elseif ($product->getStockData() && isset($product->getStockData()['is_in_stock'])) {
                $isInStock = (bool)$product->getStockData()['is_in_stock'];
            }

            // Only run if product is In Stock and Status is Enabled
            if (!$isInStock || (int)$product->getStatus() !== 1) {
                return;
            }

            // Fetch pending subscribers
            $pendingAlerts = $this->stockAlertRepository->getPendingAlertsByProduct($productId);

            if (empty($pendingAlerts)) {
                return;
            }

            $store = $this->storeManager->getDefaultStoreView();
            $storeId = (int)$store->getId();

            $senderEmail = $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?: 'patraramakrishna90@gmail.com';

            $senderName = $this->scopeConfig->getValue(
                'trans_email/ident_general/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?: 'Store Alert';

            $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);

            try {
                foreach ($pendingAlerts as $alert) {
                    try {
                        $customer = $this->customerRepository->getById($alert->getCustomerId());
                        $customerEmail = $alert->getCustomerEmail();
                        $customerName = trim($customer->getFirstname() . ' ' . $customer->getLastname()) ?: 'Customer';

                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier('codilar_instockmail_custom_template')
                            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                            ->setTemplateVars([
                                'customer_name' => $customerName,
                                'product_name'  => $product->getName(),
                                'product_url'   => $product->getProductUrl()
                            ])
                            ->setFrom(['name' => $senderName, 'email' => $senderEmail])
                            ->addTo($customerEmail)
                            ->getTransport();

                        $transport->sendMessage();

                        // Mark as handled
                        $alert->setStatus(StockAlertInterface::STATUS_HANDLED);
                        $alert->setNotifiedAt($this->dateTime->gmtDate());
                        $this->stockAlertRepository->save($alert);

                        $this->logger->info("InstockMail: Dispatched custom template to {$customerEmail} for product #{$productId}");
                    } catch (\Throwable $subEx) {
                        $this->logger->error('InstockMail Dispatch Exception: ' . $subEx->getMessage());
                    }
                }
            } finally {
                $this->emulation->stopEnvironmentEmulation();
            }

        } catch (\Throwable $e) {
            $this->logger->critical('InstockMail Root Observer Failure: ' . $e->getMessage());
        }
    }
}
