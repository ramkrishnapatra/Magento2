<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\BookingEnquiry\Model\BookingEnquiryFactory;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry as EnquiryResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Codilar_BookingEnquiry::enquiry';
    private const PERSISTOR_KEY = 'codilar_bookingenquiry_enquiry';

    public function __construct(
        Context $context,
        private readonly BookingEnquiryFactory $enquiryFactory,
        private readonly EnquiryResource $resource,
        private readonly DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        unset($data['form_key']);
        $id = (int) ($data['entity_id'] ?? 0);

        try {
            $model = $this->enquiryFactory->create();
            if ($id) {
                $this->resource->load($model, $id);
                if (!$model->getId()) {
                    throw new NoSuchEntityException(__('This enquiry no longer exists.'));
                }
            } else {
                unset($data['entity_id']);
            }

            $model->addData($data);
            $this->resource->save($model);

            $this->messageManager->addSuccessMessage(__('You saved the enquiry.'));
            $this->dataPersistor->clear(self::PERSISTOR_KEY);

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Throwable $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving.'));
        }

        // only reached on failure: keep what the user typed
        $this->dataPersistor->set(self::PERSISTOR_KEY, $data);
        return $id
            ? $resultRedirect->setPath('*/*/edit', ['entity_id' => $id])
            : $resultRedirect->setPath('*/*/new');
    }
}
