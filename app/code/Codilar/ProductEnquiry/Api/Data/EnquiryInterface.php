<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Api\Data;

interface EnquiryInterface
{
    public const ENTITY_ID  = 'entity_id';
    public const SKU        = 'sku';
    public const NAME       = 'name';
    public const EMAIL      = 'email';
    public const ADDRESS    = 'address';
    public const QTY        = 'qty';
    public const CREATED_AT = 'created_at';

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getSku(): ?string;

    /**
     * @param string $sku
     * @return EnquiryInterface
     */
    public function setSku(string $sku): EnquiryInterface;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return EnquiryInterface
     */
    public function setName(string $name): EnquiryInterface;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string $email
     * @return EnquiryInterface
     */
    public function setEmail(string $email): EnquiryInterface;

    /**
     * @return string|null
     */
    public function getAddress(): ?string;

    /**
     * @param string $address
     * @return EnquiryInterface
     */
    public function setAddress(string $address): EnquiryInterface;

    /**
     * @return int|null
     */
    public function getQty(): ?int;

    /**
     * @param int $qty
     * @return EnquiryInterface
     */
    public function setQty(int $qty): EnquiryInterface;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return EnquiryInterface
     */
    public function setCreatedAt(string $createdAt): EnquiryInterface;
}
