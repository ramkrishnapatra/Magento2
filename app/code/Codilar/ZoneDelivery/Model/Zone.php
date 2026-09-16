<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Model;

use Codilar\ZoneDelivery\Api\Data\ZoneExtensionInterface;
use Codilar\ZoneDelivery\Api\Data\ZoneInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Zone
 * Model representing delivery zone entity
 */
class Zone extends AbstractExtensibleModel implements ZoneInterface
{
    protected $_eventPrefix = 'codilar_delivery_zone';
    protected $_eventObject = 'zone';

    protected function _construct(): void
    {
        $this->_init(\Codilar\ZoneDelivery\Model\ResourceModel\Zone::class);
    }

    public function getEntityId(): ?int
    {
        $id = $this->getData(self::ENTITY_ID);
        return $id !== null ? (int)$id : null;
    }

    public function setEntityId($entityId): ZoneInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId !== null ? (int)$entityId : null);
    }

    public function getZoneReference(): ?string
    {
        return $this->getData(self::ZONE_REFERENCE);
    }

    public function setZoneReference(string $zoneReference): ZoneInterface
    {
        return $this->setData(self::ZONE_REFERENCE, $zoneReference);
    }

    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription(?string $description): ZoneInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getIsRemote(): bool
    {
        return (bool)$this->getData(self::IS_REMOTE);
    }

    public function setIsRemote(bool $isRemote): ZoneInterface
    {
        return $this->setData(self::IS_REMOTE, $isRemote);
    }

    public function getExtensionAttributes(): ?ZoneExtensionInterface
    {
        $extension = $this->_getExtensionAttributes();
        return $extension instanceof ZoneExtensionInterface ? $extension : null;
    }

    public function setExtensionAttributes(ZoneExtensionInterface $extensionAttributes): ZoneInterface
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }
}
