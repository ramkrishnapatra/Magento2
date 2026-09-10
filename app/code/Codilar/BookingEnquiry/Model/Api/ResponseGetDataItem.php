<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model\Api;

use Codilar\BookingEnquiry\Api\Data\ResponseGetDataItemInterface;

class ResponseGetDataItem extends ResponseItem implements ResponseGetDataItemInterface
{
    /**
     * Get data payload
     *
     * @return mixed
     */
    public function getDataPayload()
    {
        return $this->_getData(self::DATA);
    }

    /**
     * Set data payload
     *
     * @param mixed $data
     * @return $this
     */
    public function setDataPayload($data): self
    {
        return $this->setData(self::DATA, $data);
    }
}
