<?php
declare(strict_types=1);

namespace Codilar\InstockMail\Controller\Stock;

use Codilar\InstockMail\Api\Data\StockAlertInterface;
use Codilar\InstockMail\Api\StockAlertRepositoryInterface;
use Codilar\InstockMail\Model\StockAlertFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Add implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @var StockAlertRepositoryInterface
     */
    protected StockAlertRepositoryInterface $stockAlertRepository;

    /**
     * @var StockAlertFactory
     */
    protected StockAlertFactory $alertFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @param RequestInterface $request
     * @param CustomerSession $customerSession
     * @param StockAlertRepositoryInterface $stockAlertRepository
     * @param StockAlertFactory $alertFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        RequestInterface $request,
        CustomerSession $customerSession,
        StockAlertRepositoryInterface $stockAlertRepository,
        StockAlertFactory $alertFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->stockAlertRepository = $stockAlertRepository;
        $this->alertFactory = $alertFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute action
     *
     * @return Json
     */
    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();

        // 1. Check Customer Login
        if (!$this->customerSession->isLoggedIn()) {
            return $result->setData([
                'success' => false,
                'message' => __('Please log in to sign up for stock alerts.')
            ]);
        }

        // 2. Resolve Product ID
        $productId = (int)$this->request->getParam('product_id');
        if (!$productId) {
            $productId = (int)$this->request->getParam('product');
        }
        if (!$productId) {
            $productId = (int)$this->request->getParam('id');
        }

        if (!$productId) {
            return $result->setData([
                'success' => false,
                'message' => __('Invalid product ID.')
            ]);
        }

        $customerId = (int)$this->customerSession->getCustomerId();
        $customerEmail = (string)$this->customerSession->getCustomer()->getEmail();

        try {
            // 3. Prevent duplicate pending subscription
            $existing = $this->stockAlertRepository->getPendingAlert($customerId, $productId);
            if ($existing) {
                return $result->setData([
                    'success' => true,
                    'is_notice' => true,
                    'message' => __('You are already subscribed to receive in-stock alerts for this product.')
                ]);
            }

            // 4. Save new alert with status 'pending'
            $alert = $this->alertFactory->create();
            $alert->setCustomerId($customerId);
            $alert->setProductId($productId);
            $alert->setCustomerEmail($customerEmail);
            $alert->setStatus(StockAlertInterface::STATUS_PENDING);

            $this->stockAlertRepository->save($alert);

            return $result->setData([
                'success' => true,
                'is_notice' => false,
                'message' => __('You will be notified as soon as this product is back in stock.')
            ]);
        } catch (\Throwable $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Error: %1', $e->getMessage())
            ]);
        }
    }
}
