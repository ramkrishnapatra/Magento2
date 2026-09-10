<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Api\Data;

interface RequestItemInterface
{
    public const PHONE_NUMBER = 'phone_number';
    public const PREFERRED_SLOT = 'preferred_slot';
    public const ITEMS          = 'items';

    /**
     * @return string
     */
    public function getPhoneNumber(): string;

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber(string $phoneNumber): self;

    /**
     * @return string
     */
    public function getPreferredSlot(): string;

    /**
     * @param string $preferredSlot
     * @return $this
     */
    public function setPreferredSlot(string $preferredSlot): self;
}
