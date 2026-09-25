<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\BookingEnquiry\Api\BookingEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Codilar_BookingEnquiry::enquiry';

    public function __construct(
        Context $context,
        private BookingEnquiryRepositoryInterface $repository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $entityId = (int)$this->getRequest()->getParam('entity_id');

        if (!$entityId) {
            $this->messageManager->addErrorMessage(__('Invalid enquiry ID.'));
            return $resultRedirect->setPath('*/*/');
        }

        $response = $this->repository->deleteById($entityId);

        if ($response->getStatusCode() === 200) {
            $this->messageManager->addSuccessMessage($response->getMessage());
        } else {
            $this->messageManager->addErrorMessage($response->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
