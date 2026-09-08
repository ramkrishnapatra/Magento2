<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Index;

use Codilar\BookingEnquiry\Api\BookingEnquiryRepositoryInterface;
use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Codilar\BookingEnquiry\Model\BookingEnquiryFactory;
use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class Save implements HttpPostActionInterface
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
     * @var BookingEnquiryFactory
     */
    private BookingEnquiryFactory $bookingEnquiryFactory;

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
     * @param BookingEnquiryFactory $bookingEnquiryFactory
     * @param BookingEnquiryRepositoryInterface $enquiryRepository
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $redirectFactory,
        BookingEnquiryFactory $bookingEnquiryFactory,
        BookingEnquiryRepositoryInterface $enquiryRepository,
        MessageManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->redirectFactory = $redirectFactory;
        $this->bookingEnquiryFactory = $bookingEnquiryFactory;
        $this->enquiryRepository = $enquiryRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->redirectFactory->create();
        $data = $this->request->getPostValue();

        if (!empty($data['phone_number']) && !empty($data['preferred_slot'])) {
            try {
                $enquiry = $this->bookingEnquiryFactory->create();
                $enquiry->setPhoneNumber(strip_tags((string)$data['phone_number']));
                $enquiry->setPreferredSlot(strip_tags((string)$data['preferred_slot']));
                $enquiry->setStatus(BookingEnquiryInterface::STATUS_PENDING);

                $this->enquiryRepository->save($enquiry);

                $this->messageManager->addSuccessMessage(__('Your request has been placed! Our team will contact you.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('An error occurred while saving your request. Please try again.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please provide all required fields.'));
        }

        return $resultRedirect->setPath('booking/index/form');
    }
}
