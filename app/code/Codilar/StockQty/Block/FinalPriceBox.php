<?php
declare(strict_types=1);

namespace Codilar\StockQty\Block;

use Magento\Catalog\Pricing\Render\FinalPriceBox as DefaultFinalPriceBox;

class FinalPriceBox extends DefaultFinalPriceBox
{
    /**
     * Get Regular/Base Price safely
     *
     * @return float
     */
    public function getBaseRegularPrice(): float
    {
        $regularPriceModel = $this->getPriceType('regular_price');
        if ($regularPriceModel && $regularPriceModel->getAmount()) {
            $val = (float)$regularPriceModel->getAmount()->getValue();
            if ($val > 0) {
                return $val;
            }
        }

        // Fallback for configurable/simple items
        $product = $this->getSaleableItem();
        return (float)$product->getPrice();
    }

    /**
     * Get Final/Discounted Price safely
     *
     * @return float
     */
    public function getActualFinalPrice(): float
    {
        $finalPriceModel = $this->getPriceType('final_price');
        if ($finalPriceModel && $finalPriceModel->getAmount()) {
            return (float)$finalPriceModel->getAmount()->getValue();
        }

        $product = $this->getSaleableItem();
        return (float)$product->getFinalPrice();
    }

    /**
     * Check if product has discount
     *
     * @return bool
     */
    public function hasDiscount(): bool
    {
        $regularPrice = $this->getBaseRegularPrice();
        $finalPrice = $this->getActualFinalPrice();

        return ($regularPrice > $finalPrice && $finalPrice > 0);
    }

    /**
     * Calculate discount percentage
     *
     * @return int
     */
    public function getDiscountPercent(): int
    {
        $regularPrice = $this->getBaseRegularPrice();
        $finalPrice = $this->getActualFinalPrice();

        if ($this->hasDiscount() && $regularPrice > 0) {
            return (int)round((($regularPrice - $finalPrice) / $regularPrice) * 100);
        }

        return 0;
    }
}
