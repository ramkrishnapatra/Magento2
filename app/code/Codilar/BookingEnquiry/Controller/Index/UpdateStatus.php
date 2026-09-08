<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Index;

use Codilar\BookingEnquiry\Api\BookingEnquiryRepositoryInterface;
use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class UpdateStatus implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var RedirectFactory
     */
    private RedirectFactory $redirectFactory;

    /**
     * @var BookingEnquiryRepositoryInterface
     */
    private BookingEnquiryRepositoryInterface $enquiryRepository;

    /**
     * @var MessageManagerInterface
     */
    private MessageManagerInterface $messageManager;

    /**
     * @param RequestInterface $request
     * @param RedirectFactory $redirectFactory
     * @param BookingEnquiryRepositoryInterface $enquiryRepository
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $redirectFactory,
        BookingEnquiryRepositoryInterface $enquiryRepository,
        MessageManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->redirectFactory = $redirectFactory;
        $this->enquiryRepository = $enquiryRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->redirectFactory->create();
        $id = (int)$this->request->getParam('id');

        if ($id) {
            try {
                $enquiry = $this->enquiryRepository->getById($id);
                $enquiry->setStatus(BookingEnquiryInterface::STATUS_HANDLED);
                $this->enquiryRepository->save($enquiry);

                $this->messageManager->addSuccessMessage(__('Enquiry #%1 marked as Handled.', $id));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Unable to update status for enquiry #%1.', $id));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid enquiry ID provided.'));
        }

        return $resultRedirect->setPath('booking/index/listing');
    }
}
