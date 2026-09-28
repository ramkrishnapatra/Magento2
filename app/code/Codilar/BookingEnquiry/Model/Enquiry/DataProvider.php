<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model\Enquiry;

use Codilar\BookingEnquiry\Model\ResourceModel\BookingEnquiry\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private const PERSISTOR_KEY = 'codilar_bookingenquiry_enquiry';

    private ?array $loadedData = null;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly RequestInterface $request,
        private readonly DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }
        $this->loadedData = [];

        $id = (int) $this->request->getParam($this->getRequestFieldName());
        if ($id) {
            $this->collection->addFieldToFilter('entity_id', $id);
            foreach ($this->collection->getItems() as $item) {
                $this->loadedData[$item->getId()] = $item->getData();
            }
        }

        // data from a failed save wins over the database
        $persisted = $this->dataPersistor->get(self::PERSISTOR_KEY);
        if ($persisted) {
            $key = $persisted['entity_id'] ?? $id;
            $this->loadedData[$key] = $persisted;
            $this->dataPersistor->clear(self::PERSISTOR_KEY);
        }

        return $this->loadedData;
    }
}
