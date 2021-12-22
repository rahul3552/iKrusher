<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;
use Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\ProductSalabilityError;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class AbstractProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
abstract class AbstractProvider
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var IsProductSalableForRequestedQtyInterface
     */
    private $isProductSalableForRequestedQty;

    /**
     * @param StockRegistryInterface $stockRegistry
     * @param IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->isProductSalableForRequestedQty = $isProductSalableForRequestedQty;
    }

    /**
     * @var ProductInterface|Product
     */
    protected $product;

    /**
     * @var ProductInterface[]|Product[]
     */
    protected $subProducts = [];

    /**
     * @var string
     */
    protected $productPreparationError;

    /**
     * Set product for checking
     *
     * @param ProductInterface $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Get product for checking
     *
     * @return ProductInterface|Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Resolve and set child products
     *
     * @param ProductInterface[] $products
     */
    public function resolveAndSetSubProducts($products)
    {
        $subProducts = [];
        foreach ($products as $product) {
            if ($product->getParentProductId()) {
                $subProducts[] = $product;
            }
        }

        $this->subProducts = $subProducts;
    }

    /**
     * Get product for image
     *
     * @return ProductInterface|Product
     */
    public function getProductForImage()
    {
        return $this->getProduct();
    }

    /**
     * Set product preparation error
     *
     * @param string $error
     */
    public function setProductPreparationError($error)
    {
        $this->productPreparationError = $error;
    }

    /**
     * Get product preparation error
     *
     * @return string
     */
    public function getProductPreparationError()
    {
        return $this->productPreparationError;
    }

    /**
     * Check the scope of the product usage on specific sites
     *
     * @param int $websiteId
     * @return bool
     */
    protected function isProductInWebsite($websiteId)
    {
        return in_array($websiteId, $this->product->getWebsiteIds());
    }

    /**
     * Is available for quick order
     *
     * @param int $websiteId
     * @return bool
     */
    public function isAvailableForQuickOrder($websiteId)
    {
        return !$this->isDisabled()
            && $this->isSalable()
            && $this->isProductInWebsite($websiteId);
    }

    /**
     * Get product url
     *
     * @return string
     */
    public function getProductUrl()
    {
        $url = '';

        if ($this->product->isAvailable()) {
            $url = $this->product->getProductUrl();
        }

        return $url;
    }

    /**
     * Check product availability in site
     *
     * @param int $websiteId
     * @return bool
     */
    public function isAvailableForSite($websiteId)
    {
        return $this->isProductInWebsite($websiteId);
    }

    /**
     * Is disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->product->isDisabled();
    }

    /**
     * Is editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return $this->product->getTypeInstance()->hasRequiredOptions($this->product);
    }

    /**
     * Is salable
     *
     * @return bool
     */
    public function isSalable()
    {
        return $this->product->isSalable();
    }

    /**
     * Is qty editable
     *
     * @return bool
     */
    public function isQtyEditable()
    {
        return $this->product->isAvailable();
    }

    /**
     * Get quantity salable message
     *
     * @param float $requestedQty
     * @return string
     */
    public function getQtySalableMessage($requestedQty)
    {
        return '';
    }

    /**
     * Get product attributes
     *
     * @return array
     */
    public function getProductAttributes()
    {
        $attributes = [];
        $orderOptions = $this->getOrderOptions();
        $attributes['custom_options'] = $orderOptions['options'] ?? []; //todo render custom options
        $productOptions = $this->getProductTypeAttributes($orderOptions);
        $attributes['product_options'] = !empty($productOptions) ? $productOptions : [];

        return $attributes;
    }

    /**
     * Get final price for buy request
     *
     * This price is returned only in case buy request is specified
     *
     * @return string
     */
    public function getFinalPriceForBuyRequest()
    {
        $price = null;
        $orderOptions = $this->getOrderOptions();

        return empty($orderOptions['info_buyRequest'])
            ? $price
            : $this->product->getFinalPrice();
    }

    /**
     * Get order options
     *
     * @return array
     */
    protected function getOrderOptions()
    {
        $options['info_buyRequest'] = [];
        if (!$this->getProductPreparationError()) {
            $type = $this->product->getTypeInstance();
            $options = $type->getOrderOptions($this->product);
        }

        return $options;
    }

    /**
     * Get salable result message for sku
     *
     * @param string $sku
     * @param float|int $requestedQty
     * @return string
     * @throws LocalizedException
     */
    protected function getSalableResultMessageForSku($sku, $requestedQty)
    {
        $stockItem = $this->stockRegistry->getStockItemBySku($sku);
        $isSalableResult = $this->isProductSalableForRequestedQty->execute(
            $sku,
            $stockItem->getStockId(),
            $requestedQty
        );

        $message = '';
        if ($isSalableResult->isSalable() === false) {
            /** @var ProductSalabilityError $error */
            foreach ($isSalableResult->getErrors() as $error) {
                $message = $error->getMessage();
            }
        }

        return $message;
    }

    /**
     * Get product attributes specific for product type
     *
     * @param array $orderOptions
     * @return array
     */
    abstract protected function getProductTypeAttributes($orderOptions);
}
