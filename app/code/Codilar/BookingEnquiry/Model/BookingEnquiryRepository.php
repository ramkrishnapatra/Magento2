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
use Magento\Framework\App\RequestInterface;
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
        private RequestInterface $request,
        private ErrorProcessor $errorProcessor,
        private ResourceBookingEnquiry $resource,
        private BookingEnquiryFactory $bookingEnquiryFactory,
        private CollectionFactory $collectionFactory
    ) {
    }

    /**
     * Set dynamic HTTP code directly on response header
     */
    private function syncDynamicHttpCode(?Throwable $exception = null): void
    {
        if ($exception !== null) {
            $e = $exception instanceof Exception ? $exception : new Exception($exception->getMessage(), 0, $exception);
            $code = $this->errorProcessor->maskException($e)->getHttpCode();
            $this->httpResponse->setHttpResponseCode($code);
            return;
        }

        $this->httpResponse->setHttpResponseCode($this->request->isPost() ? 201 : 200);
    }

    /**
     * Action Response - Reads status code dynamically via getHttpResponseCode()
     */
    private function buildActionResponse(string $message, ?Throwable $exception = null): ResponseItemInterface
    {
        $this->syncDynamicHttpCode($exception);

        /** @var ResponseItemInterface $response */
        $response = $this->responseFactory->create();
        $response->setStatusCode($this->httpResponse->getHttpResponseCode());
        $response->setMessage($message);

        return $response;
    }

    /**
     * GET Response - Reads status code dynamically via getHttpResponseCode()
     */
    private function buildGetDataResponse(string $message, ?array $payload = null, ?Throwable $exception = null): ResponseGetDataItemInterface
    {
        $this->syncDynamicHttpCode($exception);

        /** @var ResponseGetDataItemInterface $response */
        $response = $this->getResponseFactory->create();
        $response->setStatusCode($this->httpResponse->getHttpResponseCode());
        $response->setMessage($message);
        $response->setDataPayload($payload);

        return $response;
    }

    public function save(RequestItemInterface $enquiry): ResponseItemInterface
    {
        try {
            $model = $this->bookingEnquiryFactory->create();

            $model->setData('phone_number', $enquiry->getPhoneNumber());
            $model->setData('preferred_slot', $enquiry->getPreferredSlot());
            $model->setData('status', 'pending');

            $this->resource->save($model);

            return $this->buildActionResponse('Booking enquiry submitted successfully.');
        } catch (Throwable $e) {
            return $this->buildActionResponse($e->getMessage(), $e);
        }
    }

    public function getById(int $entityId): ResponseGetDataItemInterface
    {
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

            return $this->buildGetDataResponse('Booking enquiry fetched successfully.', $data);
        } catch (Throwable $e) {
            return $this->buildGetDataResponse($e->getMessage(), null, $e);
        }
    }

    public function updateStatus(int $id, string $status): ResponseItemInterface
    {
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

            return $this->buildActionResponse(sprintf('Status updated to %s successfully.', $status));
        } catch (Throwable $e) {
            return $this->buildActionResponse($e->getMessage(), $e);
        }
    }

    public function getQueueList(): ResponseGetDataItemInterface
    {
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

            return $this->buildGetDataResponse('Queue list fetched successfully.', $items);
        } catch (Throwable $e) {
            return $this->buildGetDataResponse($e->getMessage(), [], $e);
        }
    }

    public function delete(BookingEnquiryInterface $enquiry): ResponseItemInterface
    {
        try {
            $this->resource->delete($enquiry);
            return $this->buildActionResponse('Booking enquiry deleted successfully.');
        } catch (Throwable $e) {
            return $this->buildActionResponse($e->getMessage(), $e);
        }
    }

    public function deleteById(int $entityId): ResponseItemInterface
    {
        try {
            $model = $this->bookingEnquiryFactory->create();
            $this->resource->load($model, $entityId);

            if (!$model->getId()) {
                throw new NoSuchEntityException(__('Booking enquiry with ID %1 does not exist.', $entityId));
            }

            $this->resource->delete($model);

            return $this->buildActionResponse(sprintf('Enquiry ID %d deleted successfully.', $entityId));
        } catch (Throwable $e) {
            return $this->buildActionResponse($e->getMessage(), $e);
        }
    }

    /**
     * Admin queue fetch: Returns all rows with complete details
     *
     * @return ResponseGetDataItemInterface
     */
    public function getAdminQueueList(): ResponseGetDataItemInterface
    {
        try {
            /** @var \Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->setOrder('entity_id', 'DESC'); // Admin ke liye latest records pehle show karna better practice hai

            $items = [];
            foreach ($collection as $item) {
                $raw = $item->getData();
                $items[] = [
                    'entity_id'      => (int)$raw['entity_id'],
                    'phone_number'   => (string)($raw['phone_number'] ?? ''),
                    'preferred_slot' => (string)($raw['preferred_slot'] ?? ''),
                    'status'         => (string)($raw['status'] ?? ''),
                    'created_at'     => (string)($raw['created_at'] ?? ''),
                    'updated_at'     => (string)($raw['updated_at'] ?? '') // Admin ko updated_at bhi provide kar sakte hain
                ];
            }

            return $this->buildGetDataResponse('Admin queue list fetched successfully.', $items);
        } catch (Throwable $e) {
            return $this->buildGetDataResponse($e->getMessage(), [], $e);
        }
    }
}
