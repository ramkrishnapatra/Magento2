<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\PincodeExtensionInterface;
use Codilar\ZoneDelivery\Api\Data\PincodeInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Pincode
 * Model representing delivery pincode mapping entity
 */
class Pincode extends AbstractExtensibleModel implements PincodeInterface
{
    protected $_eventPrefix = 'codilar_delivery_pincode';
    protected $_eventObject = 'pincode';

    protected function _construct(): void
    {
        $this->_init(\Codilar\ZoneDelivery\Model\ResourceModel\Pincode::class);
    }

    public function getEntityId(): ?int
    {
        $id = $this->getData(self::ENTITY_ID);
        return $id !== null ? (int)$id : null;
    }

    public function setEntityId($entityId): PincodeInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId !== null ? (int)$entityId : null);
    }

    public function getPincodeFrom()
    {
        return $this->getData(self::PINCODE_FROM);
    }

    public function setPincodeFrom($pincodeFrom)
    {
        return $this->setData(self::PINCODE_FROM, $pincodeFrom);
    }

    public function getPincodeTo()
    {
        return $this->getData(self::PINCODE_TO);
    }

    public function setPincodeTo($pincodeTo)
    {
        return $this->setData(self::PINCODE_TO, $pincodeTo);
    }
    public function getZoneId(): ?int
    {
        $zoneId = $this->getData(self::ZONE_ID);
        return $zoneId !== null ? (int)$zoneId : null;
    }

    public function setZoneId(int $zoneId): PincodeInterface
    {
        return $this->setData(self::ZONE_ID, $zoneId);
    }

    public function getExtensionAttributes(): ?PincodeExtensionInterface
    {
        $extension = $this->_getExtensionAttributes();
        return $extension instanceof PincodeExtensionInterface ? $extension : null;
    }

    public function setExtensionAttributes(PincodeExtensionInterface $extensionAttributes): PincodeInterface
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }
}
