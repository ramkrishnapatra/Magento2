<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Api\Data;

interface ResponseItemInterface
{
    public const STATUS_CODE = 'status_code';
    public const MESSAGE = 'message';

    /**
     * @return int|null
     */
    public function getStatusCode(): ?int;

    /**
     * @param int|null $statusCode
     * @return $this
     */
    public function setStatusCode(?int $statusCode): self;

    /**
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * @param string|null $message
     * @return $this
     */
    public function setMessage(?string $message): self;
}
