<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Adminhtml\Enquiry;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
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
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Codilar_BookingEnquiry::enquiry');
        $page->getConfig()->getTitle()->prepend(__('Booking Enquiry'));
        return $page;
    }
}
