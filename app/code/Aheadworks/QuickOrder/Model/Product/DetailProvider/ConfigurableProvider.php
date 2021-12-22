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

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\QuickOrder\Model\Product\Checker\Configurable as ConfigurableProductChecker;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;

/**
 * Class ConfigurableProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class ConfigurableProvider extends AbstractProvider
{
    /**
     * @var ConfigurableProductChecker
     */
    private $configurableProductChecker;

    /**
     * @param StockRegistryInterface $stockRegistry
     * @param IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
     * @param ConfigurableProductChecker $configurableProductChecker
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty,
        ConfigurableProductChecker $configurableProductChecker
    ) {
        parent::__construct(
            $stockRegistry,
            $isProductSalableForRequestedQty
        );
        $this->configurableProductChecker = $configurableProductChecker;
    }

    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return isset($orderOptions['attributes_info']) ? array_values($orderOptions['attributes_info']) : [];
    }

    /**
     * @inheritdoc
     */
    public function getProductForImage()
    {
        $productForImage = $this->getProduct();
        $childProduct = $this->getChildProduct();
        if ($childProduct !== null
            && $this->configurableProductChecker->isNeedToUseChildProductImage($childProduct)
        ) {
            $productForImage = $childProduct;
        }
        return $productForImage;
    }

    /**
     * @inheritdoc
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function getQtySalableMessage($requestedQty)
    {
        $product = $this->getChildProduct();
        if (!$product) {
            return '';
        }

        return $this->getSalableResultMessageForSku($product->getSku(), $requestedQty);
    }

    /**
     * Retrieve child product
     *
     * @return ProductInterface|Product|null
     */
    protected function getChildProduct()
    {
        return !empty($this->subProducts) ? reset($this->subProducts) : null;
    }
}
