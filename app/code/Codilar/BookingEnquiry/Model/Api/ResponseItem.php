<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model\Api;

use Codilar\BookingEnquiry\Api\Data\ResponseItemInterface;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\DataObject;

class ResponseItem extends DataObject implements ResponseItemInterface
{
    protected HttpResponse $httpResponse;

    public function __construct(
        HttpResponse $httpResponse,
        array $data = []
    ) {
        $this->httpResponse = $httpResponse;
        parent::__construct($data);
    }

    public function getStatusCode(): ?int
    {
        $code = $this->_getData(self::STATUS_CODE);
        if ($code === null) {
            $code = $this->httpResponse->getHttpResponseCode() ?: 200;
        }
        return (int)$code;
    }

    public function setStatusCode(?int $statusCode): self
    {
        return $this->setData(self::STATUS_CODE, $statusCode);
    }

    public function getMessage(): ?string
    {
        $message = $this->_getData(self::MESSAGE);
        return $message !== null ? (string)$message : null;
    }

    public function setMessage(?string $message): self
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
