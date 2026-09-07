<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Codilar\ProductEnquiry\Api\EnquiryRepositoryInterface;
use Codilar\ProductEnquiry\Model\Logger\EnquiryLogger;

class ProductEnquiry implements HttpPostActionInterface
{
    protected RequestInterface $request;
    protected JsonFactory $jsonFactory;
    protected EnquiryRepositoryInterface $enquiryRepository;
    protected EnquiryLogger $logger;

    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory,
        EnquiryRepositoryInterface $enquiryRepository,
        EnquiryLogger $logger
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->enquiryRepository = $enquiryRepository;
        $this->logger = $logger;
    }

    public function execute(): \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
    {
        $result = $this->jsonFactory->create();

        $sku     = (string)$this->request->getParam('sku');
        $name    = (string)$this->request->getParam('name');
        $email   = (string)$this->request->getParam('email');
        $address = (string)$this->request->getParam('address');
        $qty     = (int)$this->request->getParam('qty', 1);

        $this->logger->info(sprintf(
            'HIT RECEIVED - SKU: %s, Name: %s, Email: %s, Qty: %d',
            $sku,
            $name,
            $email,
            $qty
        ));

        if (empty($name) || empty($email) || empty($sku)) {
            $this->logger->warning('Validation Failed: Required fields missing.');
            return $result->setData([
                'success' => false,
                'message' => __('Please fill all required fields.')
            ]);
        }

        try {
            $enquiry = $this->enquiryRepository->create();
            $enquiry->setSku($sku)
                ->setName($name)
                ->setEmail($email)
                ->setAddress($address)
                ->setQty($qty);

            $savedEnquiry = $this->enquiryRepository->save($enquiry);

            $this->logger->info('DB Save SUCCESS - Entity ID: ' . $savedEnquiry->getId());

            return $result->setData([
                'success' => true,
                'message' => __('Enquiry submitted successfully! Entity ID: ' . $savedEnquiry->getId())
            ]);
        } catch (\Exception $e) {
            $this->logger->error('DB Save FAILED - ' . $e->getMessage());

            return $result->setData([
                'success' => false,
                'message' => __('Error saving enquiry: ' . $e->getMessage())
            ]);
        }
    }
}
