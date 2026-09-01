<?php
namespace Codilar\StockQty\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;

class CheckGuestCartTotal implements ObserverInterface
{
    protected $customerSession;
    protected $checkoutSession;
    protected $messageManager;
    protected $response;
    protected $url;
    protected $actionFlag;

    public function __construct(
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        MessageManagerInterface $messageManager,
        ResponseHttp $response,
        UrlInterface $url,
        ActionFlag $actionFlag
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
        $this->response = $response;
        $this->url = $url;
        $this->actionFlag = $actionFlag;
    }

    public function execute(Observer $observer)
    {
        if (!$this->customerSession->isLoggedIn()) {
            $quote = $this->checkoutSession->getQuote();
            $grandTotal = (float) $quote->getGrandTotal();

            if ($grandTotal < 500) {
                $this->messageManager->addErrorMessage(
                    __('If you want to purchase less than 500, please log in first.')
                );

                // Stop action dispatch using ActionInterface constant
                $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);

                // Redirect to Login page
                $this->response->setRedirect($this->url->getUrl('customer/account/login'));
            }
        }
    }
}
