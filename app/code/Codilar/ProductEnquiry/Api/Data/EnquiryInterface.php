<?php
namespace Codilar\ProductEnquiry\Api\Data;

interface EnquiryInterface
{
    const ENTITY_ID  = 'entity_id';
    const SKU        = 'sku';
    const NAME       = 'name';
    const EMAIL      = 'email';
    const ADDRESS    = 'address';
    const QTY        = 'qty';
    const CREATED_AT = 'created_at';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getSku();

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return string|null
     */
    public function getAddress();

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * @return int|null
     */
    public function getQty();

    /**
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
