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
namespace Aheadworks\QuickOrder\Model\Product\View;

use Magento\Framework\Registry;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\Product\BuyRequest\Processor;

/**
 * Class DataApplier
 *
 * @package Aheadworks\QuickOrder\Model\Product\View
 */
class DataApplier
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Processor
     */
    private $buyRequestProcessor;

    /**
     * @param Registry $coreRegistry
     * @param Processor $buyRequestProcessor
     */
    public function __construct(
        Registry $coreRegistry,
        Processor $buyRequestProcessor
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->buyRequestProcessor = $buyRequestProcessor;
    }

    /**
     * Apply product data required for product view rendering
     *
     * @param ProductInterface|Product $product
     * @param ProductListItemInterface $item
     */
    public function apply($product, $item)
    {
        $this->coreRegistry->register('product', $product);
        $this->coreRegistry->register('current_product', $product);

        $buyRequest = $this->buyRequestProcessor->prepareBuyRequest($item);
        $optionValues = $product->processBuyRequest($buyRequest);
        $product->setPreconfiguredValues($optionValues);
        $product->setConfigureMode(true);
    }
}
