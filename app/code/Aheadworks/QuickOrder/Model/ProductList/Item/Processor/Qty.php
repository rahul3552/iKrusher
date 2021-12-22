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
namespace Aheadworks\QuickOrder\Model\ProductList\Item\Processor;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Aheadworks\QuickOrder\Model\ProductList\Item\ProcessorInterface;

/**
 * Class Qty
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item\Processor
 */
class Qty implements ProcessorInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        StockRegistryInterface $stockRegistry
    ) {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @inheritdoc
     */
    public function process($requestItem, $item, $product)
    {
        $requestedQty = $item->getProductQty() ?? 0;
        if (!empty($requestItem->getProductQty())) {
            $requestedQty = $requestItem->getProductQty();
        }

        if ($product->getTypeId() == Grouped::TYPE_CODE) {
            $requestedQty = 1;
        }

        $item->setProductQty($this->getQty($product, $requestedQty));
    }

    /**
     * Get product quantity
     *
     * @param ProductInterface $product
     * @param float $requestedQty
     * @return float
     */
    private function getQty($product, $requestedQty)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $minimumQty = $stockItem->getMinSaleQty();
        if ($minimumQty && $minimumQty > 0 && !$requestedQty) {
            $requestedQty = $minimumQty;
        }

        return $requestedQty;
    }
}
