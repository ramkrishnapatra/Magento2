<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\BookingEnquiry\Api\BookingEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry\CollectionFactory;

class MassDelete extends Action
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


        $deleted = 0;
        foreach ($collection->getItems() as $item) {
            $response = $this->repository->deleteById((int)$item->getId());
            if ($response->getStatusCode() === 200) {
                $deleted++;
            }
        }

        $this->messageManager->addSuccessMessage(__('%1 enquiry(ies) deleted.', $deleted));

        return $resultRedirect->setPath('*/*/');
    }
}
