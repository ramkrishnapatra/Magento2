<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\BookingEnquiry\Api\BookingEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry\CollectionFactory;

class MassHandle extends Action
{
    const ADMIN_RESOURCE = 'Codilar_BookingEnquiry::enquiry';

    public function __construct(
        Context $context,
        private Filter $filter,
        private CollectionFactory $collectionFactory,
        private BookingEnquiryRepositoryInterface $repository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $handled = 0;
        $skipped = 0;

        foreach ($collection->getItems() as $item) {
            $response = $this->repository->updateStatus((int)$item->getId(), 'handled');
            if ($response->getStatusCode() === 200) {
                $handled++;
            } else {
                $skipped++;
            }
        }

        if ($handled) {
            $this->messageManager->addSuccessMessage(__('%1 enquiry(ies) marked as handled.', $handled));
        }
        if ($skipped) {
            $this->messageManager->addWarningMessage(__('%1 enquiry(ies) were skipped (already handled).', $skipped));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
