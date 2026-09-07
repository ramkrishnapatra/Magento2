<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Form implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @param PageFactory $resultPageFactory
     */
    public function __construct(PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Render the booking enquiry form page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $page = $this->resultPageFactory->create();
        $page->getConfig()->getTitle()->set(__('Book a Call Enquiry'));
        return $page;
    }
}
