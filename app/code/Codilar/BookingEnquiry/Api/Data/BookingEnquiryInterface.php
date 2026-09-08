<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Api\Data;

interface BookingEnquiryInterface
{
    public const ENTITY_ID = 'entity_id';
    public const PHONE_NUMBER = 'phone_number';
    public const PREFERRED_SLOT = 'preferred_slot';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';

    public const STATUS_PENDING = 'pending';
    public const STATUS_HANDLED = 'handled';

    /**
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId(int $entityId): self;

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string;

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber(string $phoneNumber): self;

    /**
     * @return string|null
     */
    public function getPreferredSlot(): ?string;

    /**
     * @param string $preferredSlot
     * @return $this
     */
    public function setPreferredSlot(string $preferredSlot): self;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;
}
