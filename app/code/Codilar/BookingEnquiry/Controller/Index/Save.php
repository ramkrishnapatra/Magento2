<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Index;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
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
     * @var ResourceConnection
     */
    private ResourceConnection $resource;

    /**
     * @var MessageManagerInterface
     */
    private MessageManagerInterface $messageManager;

    /**
     * @param RequestInterface $request
     * @param RedirectFactory $redirectFactory
     * @param ResourceConnection $resource
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $redirectFactory,
        ResourceConnection $resource,
        MessageManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->redirectFactory = $redirectFactory;
        $this->resource = $resource;
        $this->messageManager = $messageManager;
    }

    /**
     * Execute save booking enquiry
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->redirectFactory->create();
        $data = $this->request->getPostValue();

        if (!empty($data['phone_number']) && !empty($data['preferred_slot'])) {
            try {
                $connection = $this->resource->getConnection();
                $tableName = $this->resource->getTableName('booking_enquiry');

                $connection->insert($tableName, [
                    'phone_number'   => strip_tags((string)$data['phone_number']),
                    'preferred_slot' => strip_tags((string)$data['preferred_slot']),
                    'status'         => 'pending'
                ]);

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
