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

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\Codilar\ZoneDelivery\Model\ResourceModel\Zone::class);
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        $id = $this->getData(self::ENTITY_ID);
        return $id !== null ? (int)$id : null;
    }

    /**
     * @param $entityId
     * @return ZoneInterface
     */
    public function setEntityId($entityId): ZoneInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId !== null ? (int)$entityId : null);
    }

    /**
     * @return string|null
     */
    public function getZoneReference(): ?string
    {
        return $this->getData(self::ZONE_REFERENCE);
    }

    /**
     * @param string $zoneReference
     * @return ZoneInterface
     */
    public function setZoneReference(string $zoneReference): ZoneInterface
    {
        return $this->setData(self::ZONE_REFERENCE, $zoneReference);
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string|null $description
     * @return ZoneInterface
     */
    public function setDescription(?string $description): ZoneInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return bool
     */
    public function getIsRemote(): bool
    {
        return (bool)$this->getData(self::IS_REMOTE);
    }

    /**
     * @param bool $isRemote
     * @return ZoneInterface
     */
    public function setIsRemote(bool $isRemote): ZoneInterface
    {
        return $this->setData(self::IS_REMOTE, $isRemote);
    }

    /**
     * @return ZoneExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ZoneExtensionInterface
    {
        $extension = $this->_getExtensionAttributes();
        return $extension instanceof ZoneExtensionInterface ? $extension : null;
    }

    /**
     * @param ZoneExtensionInterface $extensionAttributes
     * @return ZoneInterface
     */
    public function setExtensionAttributes(ZoneExtensionInterface $extensionAttributes): ZoneInterface
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }
}
