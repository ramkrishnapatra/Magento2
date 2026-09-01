<?php
declare(strict_types=1);

namespace Codilar\SameCategory\Block\Widget;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

class SameCategory extends Template implements BlockInterface
{
    protected $_template = 'Codilar_SameCategory::widget/same_category.phtml';

    private CollectionFactory $productCollectionFactory;
    private ImageFactory $imageFactory;
    private PostHelper $postHelper;
    private WishlistHelper $wishlistHelper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        ImageFactory $imageFactory,
        PostHelper $postHelper,
        WishlistHelper $wishlistHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageFactory = $imageFactory;
        $this->postHelper = $postHelper;
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * Retrieve the current product without deprecated Registry
     */
    public function getCurrentProduct(): ?Product
    {
        if ($this->hasData('current_product')) {
            return $this->getData('current_product');
        }

        $productBlock = $this->getLayout()->getBlock('product.info');
        if ($productBlock && method_exists($productBlock, 'getProduct')) {
            return $productBlock->getProduct();
        }

        return null;
    }

    /**
     * Get products belonging to the same category
     */
    public function getCategoryProducts(): ?Collection
    {
        $currentProduct = $this->getCurrentProduct();
        if (!$currentProduct) {
            return null;
        }

        $categoryIds = $currentProduct->getCategoryIds();
        if (empty($categoryIds)) {
            return null;
        }

        $limit = (int) $this->getData('limit') ?: 8;

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'small_image', 'thumbnail', 'url_key', 'special_price'])
            ->addStoreFilter((int)$this->_storeManager->getStore()->getId())
            ->addCategoriesFilter(['in' => $categoryIds])
            ->addAttributeToFilter('entity_id', ['neq' => $currentProduct->getId()])
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', ['in' => [
                Visibility::VISIBILITY_IN_CATALOG,
                Visibility::VISIBILITY_BOTH
            ]])
            ->setPageSize($limit)
            ->setCurPage(1);

        return $collection;
    }

    /**
     * Render product image block via ImageFactory
     */
    public function getImage(Product $product, string $imageId = 'category_page_grid', array $attributes = []): Image
    {
        return $this->imageFactory->create($product, $imageId, $attributes);
    }

    /**
     * Render price HTML
     */
    public function getProductPriceHtml(Product $product): string
    {
        /** @var PricingRender $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                PricingRender::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        if ($priceRender) {
            return $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container'     => true,
                    'display_minimal_price' => true,
                    'zone'                  => PricingRender::ZONE_ITEM_LIST,
                    'list_category_page'    => true
                ]
            );
        }

        return '';
    }

    /**
     * Generate wishlist post data params
     */
    public function getAddToWishlistParams(Product $product): string
    {
        return $this->wishlistHelper->getAddParams($product);
    }

    /**
     * Generate add to compare post params
     */
    public function getAddToCompareParams(Product $product): string
    {
        return $this->postHelper->getPostData(
            $this->getUrl('catalog/product_compare/add'),
            ['product' => $product->getId()]
        );
    }
}
