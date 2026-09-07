<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Index;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
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
     * Update enquiry status to handled
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->redirectFactory->create();
        $id = (int)$this->request->getParam('id');

        if ($id) {
            try {
                $connection = $this->resource->getConnection();
                $tableName = $this->resource->getTableName('booking_enquiry');

                $connection->update(
                    $tableName,
                    ['status' => 'handled'],
                    ['entity_id = ?' => $id]
                );

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
