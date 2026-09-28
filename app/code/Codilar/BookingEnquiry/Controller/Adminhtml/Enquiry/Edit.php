<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Adminhtml\Enquiry;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Codilar_BookingEnquiry::enquiry';

    public function __construct(
        Context $context,
        private readonly PageFactory $pageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Codilar_BookingEnquiry::enquiry');
        $page->getConfig()->getTitle()->prepend(
            $id ? __('Edit Enquiry #%1', $id) : __('New Enquiry')
        );
        return $page;
    }
}
