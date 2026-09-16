<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model;

use Codilar\BookingEnquiry\Api\BookingEnquiryRepositoryInterface;
use Codilar\BookingEnquiry\Api\Data\BookingEnquiryInterface;
use Codilar\BookingEnquiry\Api\Data\RequestItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseItemInterface;
use Codilar\BookingEnquiry\Api\Data\ResponseItemInterfaceFactory;
use Codilar\BookingEnquiry\Model\Api\ResponseGetDataItemFactory;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry as ResourceBookingEnquiry;
use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry\CollectionFactory;
use Exception;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\ErrorProcessor;
use Throwable;

class BookingEnquiryRepository implements BookingEnquiryRepositoryInterface
{
    public function __construct(
        private ResponseItemInterfaceFactory $responseFactory,
        private ResponseGetDataItemFactory $getResponseFactory,
        private HttpResponse $httpResponse,
        private ErrorProcessor $errorProcessor,
        private ResourceBookingEnquiry $resource,
        private BookingEnquiryFactory $bookingEnquiryFactory,
        private CollectionFactory $collectionFactory
    ) {
    }

    /**
     * Set dynamic HTTP code directly on response header and return the integer code
     */
    private function syncDynamicHttpCode(Throwable $exception): int
    {
        $e = $exception instanceof Exception ? $exception : new Exception($exception->getMessage(), 0, $exception);
        $code = (int)$this->errorProcessor->maskException($e)->getHttpCode();
        $this->httpResponse->setHttpResponseCode($code);
        return $code;
    }

    public function save(RequestItemInterface $enquiry): ResponseItemInterface
    {
        /** @var ResponseItemInterface $response */
        $response = $this->responseFactory->create();
        try {
            $model = $this->bookingEnquiryFactory->create();

            $model->setData('phone_number', $enquiry->getPhoneNumber());
            $model->setData('preferred_slot', $enquiry->getPreferredSlot());
            $model->setData('status', 'pending');

            $this->resource->save($model);

            $this->httpResponse->setHttpResponseCode(201);
            $response->setStatusCode(201);
            $response->setMessage('Booking enquiry submitted successfully.');

            return $response;
        } catch (Throwable $e) {
            $code = $this->syncDynamicHttpCode($e);
            $response->setStatusCode($code);
            $response->setMessage($e->getMessage());

            return $response;
        }
    }

    public function getById(int $entityId): ResponseGetDataItemInterface
    {
        /** @var ResponseGetDataItemInterface $response */
        $response = $this->getResponseFactory->create();
        try {
            $model = $this->bookingEnquiryFactory->create();
            $this->resource->load($model, $entityId);

            if (!$model->getId()) {
                throw new NoSuchEntityException(__('Booking enquiry with ID %1 does not exist.', $entityId));
            }

            $data = [
                'entity_id'      => (int)$model->getId(),
                'phone_number'   => (string)$model->getData('phone_number'),
                'preferred_slot' => (string)$model->getData('preferred_slot'),
                'status'         => (string)$model->getData('status'),
                'created_at'     => (string)$model->getData('created_at')
            ];

            $this->httpResponse->setHttpResponseCode(200);
            $response->setStatusCode(200);
            $response->setMessage('Booking enquiry fetched successfully.');
            $response->setDataPayload($data);

            return $response;
        } catch (Throwable $e) {
            $code = $this->syncDynamicHttpCode($e);
            $response->setStatusCode($code);
            $response->setMessage($e->getMessage());
            $response->setDataPayload(null);

            return $response;
        }
    }

    public function updateStatus(int $id, string $status): ResponseItemInterface
    {
        /** @var ResponseItemInterface $response */
        $response = $this->responseFactory->create();
        try {
            $model = $this->bookingEnquiryFactory->create();
            $this->resource->load($model, $id);

            if (!$model->getId()) {
                throw new NoSuchEntityException(
                    __('Booking enquiry with ID %1 does not exist.', $id)
                );
            }

            $currentStatus = strtolower((string)$model->getData('status'));

            if ($currentStatus === 'handled') {
                throw new LocalizedException(
                    __('This booking enquiry (ID: %1) is already marked as handled and cannot be updated.', $id)
                );
            }

            if ($currentStatus === strtolower($status)) {
                throw new LocalizedException(
                    __('Booking enquiry is already in "%1" status.', $status)
                );
            }

            $model->setData('status', $status);
            $this->resource->save($model);

            $this->httpResponse->setHttpResponseCode(200);
            $response->setStatusCode(200);
            $response->setMessage(sprintf('Status updated to %s successfully.', $status));

            return $response;
        } catch (Throwable $e) {
            $code = $this->syncDynamicHttpCode($e);
            $response->setStatusCode($code);
            $response->setMessage($e->getMessage());

            return $response;
        }
    }

    public function getQueueList(): ResponseGetDataItemInterface
    {
        /** @var ResponseGetDataItemInterface $response */
        $response = $this->getResponseFactory->create();
        try {
            $collection = $this->collectionFactory->create();
            $collection->setOrder('entity_id', 'ASC');

            $items = [];
            foreach ($collection as $item) {
                $raw = $item->getData();
                $items[] = [
                    'entity_id'      => (int)$raw['entity_id'],
                    'phone_number'   => (string)($raw['phone_number'] ?? ''),
                    'preferred_slot' => (string)($raw['preferred_slot'] ?? ''),
                    'status'         => (string)($raw['status'] ?? ''),
                    'created_at'     => (string)($raw['created_at'] ?? '')
                ];
            }

            $this->httpResponse->setHttpResponseCode(200);
            $response->setStatusCode(200);
            $response->setMessage('Queue list fetched successfully.');
            $response->setDataPayload($items);

            return $response;
        } catch (Throwable $e) {
            $code = $this->syncDynamicHttpCode($e);
            $response->setStatusCode($code);
            $response->setMessage($e->getMessage());
            $response->setDataPayload([]);

            return $response;
        }
    }

    public function delete(BookingEnquiryInterface $enquiry): ResponseItemInterface
    {
        /** @var ResponseItemInterface $response */
        $response = $this->responseFactory->create();
        try {
            $this->resource->delete($enquiry);

            $this->httpResponse->setHttpResponseCode(200);
            $response->setStatusCode(200);
            $response->setMessage('Booking enquiry deleted successfully.');

            return $response;
        } catch (Throwable $e) {
            $code = $this->syncDynamicHttpCode($e);
            $response->setStatusCode($code);
            $response->setMessage($e->getMessage());

            return $response;
        }
    }

    public function deleteById(int $entityId): ResponseItemInterface
    {
        /** @var ResponseItemInterface $response */
        $response = $this->responseFactory->create();
        try {
            $model = $this->bookingEnquiryFactory->create();
            $this->resource->load($model, $entityId);

            if (!$model->getId()) {
                throw new NoSuchEntityException(__('Booking enquiry with ID %1 does not exist.', $entityId));
            }

            $this->resource->delete($model);

            $this->httpResponse->setHttpResponseCode(200);
            $response->setStatusCode(200);
            $response->setMessage(sprintf('Enquiry ID %d deleted successfully.', $entityId));

            return $response;
        } catch (Throwable $e) {
            $code = $this->syncDynamicHttpCode($e);
            $response->setStatusCode($code);
            $response->setMessage($e->getMessage());

            return $response;
        }
    }

    public function getAdminQueueList(): ResponseGetDataItemInterface
    {
        /** @var ResponseGetDataItemInterface $response */
        $response = $this->getResponseFactory->create();
        try {
            $collection = $this->collectionFactory->create();
            $collection->setOrder('entity_id', 'DESC');

            $items = [];
            foreach ($collection as $item) {
                $raw = $item->getData();
                $items[] = [
                    'entity_id'      => (int)$raw['entity_id'],
                    'phone_number'   => (string)($raw['phone_number'] ?? ''),
                    'preferred_slot' => (string)($raw['preferred_slot'] ?? ''),
                    'status'         => (string)($raw['status'] ?? ''),
                    'created_at'     => (string)($raw['created_at'] ?? ''),
                    'updated_at'     => (string)($raw['updated_at'] ?? '')
                ];
            }

            $this->httpResponse->setHttpResponseCode(200);
            $response->setStatusCode(200);
            $response->setMessage('Admin queue list fetched successfully.');
            $response->setDataPayload($items);

            return $response;
        } catch (Throwable $e) {
            $code = $this->syncDynamicHttpCode($e);
            $response->setStatusCode($code);
            $response->setMessage($e->getMessage());
            $response->setDataPayload([]);

            return $response;
        }
    }
}
