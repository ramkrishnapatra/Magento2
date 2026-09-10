<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Cron;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Codilar\InstockMail\Api\StockAlertRepositoryInterface;
use Codilar\InstockMail\Model\ResourceModel\StockAlert\CollectionFactory as AlertCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SendStockAlertEmails
{
    protected AlertCollectionFactory $alertCollectionFactory;
    protected StockAlertRepositoryInterface $stockAlertRepository;
    protected ProductRepositoryInterface $productRepository;
    protected CustomerRepositoryInterface $customerRepository;
    protected TransportBuilder $transportBuilder;
    protected ScopeConfigInterface $scopeConfig;
    protected StoreManagerInterface $storeManager;
    protected Emulation $emulation;
    protected DateTime $dateTime;
    protected LoggerInterface $logger;

    public function __construct(
        AlertCollectionFactory $alertCollectionFactory,
        StockAlertRepositoryInterface $stockAlertRepository,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Emulation $emulation,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->alertCollectionFactory = $alertCollectionFactory;
        $this->stockAlertRepository = $stockAlertRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->emulation = $emulation;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Execute stock alert cron job
     */
    public function execute(): void
    {
        try {
            $alertCollection = $this->alertCollectionFactory->create();
            $alertCollection->addFieldToFilter('status', StockAlertInterface::STATUS_PENDING);

            if ($alertCollection->getSize() === 0) {
                return;
            }

            $store = $this->storeManager->getDefaultStoreView();
            $storeId = (int)$store->getId();

            $senderEmail = $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?: 'sales@example.com';

            $senderName = $this->scopeConfig->getValue(
                'trans_email/ident_general/name',
                ScopeInterface::SCOPE_STORE,
                $storeId
            ) ?: 'Store Alert';

            // Emulate store frontend environment to resolve scope-based URLs and stock
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

            try {
                foreach ($alertCollection as $alert) {
                    try {
                        $productId = (int)$alert->getProductId();
                        $product = $this->productRepository->getById($productId, false, $storeId, true);

                        // Non-deprecated stock & salability verification within active emulation
                        $isSalable = (bool)$product->isSalable();
                        $isEnabled = (int)$product->getStatus() === ProductStatus::STATUS_ENABLED;

                        if (!$isSalable || !$isEnabled) {
                            continue;
                        }

                        $customerEmail = $alert->getCustomerEmail();
                        $customerName = 'Customer';
                        $customerId = (int)$alert->getCustomerId();

                        if ($customerId > 0) {
                            try {
                                $customer = $this->customerRepository->getById($customerId);
                                $fullName = trim($customer->getFirstname() . ' ' . $customer->getLastname());
                                if (!empty($fullName)) {
                                    $customerName = $fullName;
                                }
                            } catch (NoSuchEntityException $e) {
                                // Fallback to 'Customer' if record is missing
                            }
                        }

                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier('codilar_instockmail_custom_template')
                            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                            ->setTemplateVars([
                                'customer_name' => $customerName,
                                'product_name'  => $product->getName(),
                                'product_url'   => $product->getProductUrl()
                            ])
                            ->setFromByScope([
                                'name'  => $senderName,
                                'email' => $senderEmail
                            ], $storeId)
                            ->addTo($customerEmail)
                            ->getTransport();

                        $transport->sendMessage();

                        // Update alert status
                        $alert->setStatus(StockAlertInterface::STATUS_HANDLED);
                        $alert->setNotifiedAt($this->dateTime->gmtDate());
                        $this->stockAlertRepository->save($alert);

                        $this->logger->info("Cron [codilar_cron_group]: Mail dispatched to {$customerEmail} for product #{$productId}");
                    } catch (\Throwable $subEx) {
                        $this->logger->error('Cron Item Dispatch Error: ' . $subEx->getMessage());
                    }
                }
            } finally {
                $this->emulation->stopEnvironmentEmulation();
            }
        } catch (\Throwable $e) {
            $this->logger->critical('Custom Cron Group Failure: ' . $e->getMessage());
        }
    }
}
