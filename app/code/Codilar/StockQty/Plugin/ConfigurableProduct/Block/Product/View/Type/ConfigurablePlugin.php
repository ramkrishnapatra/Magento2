<?php
namespace Codilar\StockQty\Plugin\ConfigurableProduct\Block\Product\View\Type;

use Magento\CatalogInventory\Api\StockRegistryInterface; //here I will get quantity
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable; //here I will do configuration
use Magento\Framework\Serialize\Serializer\Json; //json encode ,decode

class ConfigurablePlugin
{
    protected $json; //Declares a protected class property to hold the instance of Magento's Json serializer.

    protected $stockRegistry; //Declares a protected class property to hold the instance of StockRegistryInterface.

    public function __construct(
        Json $json, //creating json object
        StockRegistryInterface $stockRegistry //creating stockRegistryInterface object
    ) {
        $this->json = $json;
        $this->stockRegistry = $stockRegistry;
    }

    public function afterGetJsonConfig(Configurable $subject, $resultJson) //Configurable class object
    {
        $config = $this->json->unserialize($resultJson); //here creating array of all child product
        $stockData = []; //associative array key will be chil product and value will be qty

        foreach ($subject->getAllowProducts() as $childProduct) { //its returning all enable child products
            $stockItem = $this->stockRegistry->getStockItem($childProduct->getId());
            //stockRegistry class ka getStock will return StockItemInterface and StockItemInterface contain all data about product
            $stockData[$childProduct->getId()] = [
                'qty' => (int)$stockItem->getQty(),
                'sku' => $childProduct->getSku()
            ];
            //StockItemInterface contain this functions
        }

        $config['child_stock_qty'] = $stockData;

        return $this->json->serialize($config);
    }
}
