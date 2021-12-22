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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\ViewModel\Email\Quote\Item;

use Aheadworks\Ctq\ViewModel\Customer\Quote\Edit\Item as EditQuoteItemViewModel;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Catalog\Block\Product\ImageFactory as ProductImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface
    as ProductItemResolverInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Class DefaultRenderer
 *
 * @package Aheadworks\Ctq\ViewModel\Email\Quote\Item
 */
class DefaultRenderer extends EditQuoteItemViewModel
{
    /**
     * @var ProductImageFactory
     */
    private $productImageFactory;

    /**
     * @var ProductItemResolverInterface
     */
    private $productItemResolver;

    /**
     * @param ConfigurationPool $configurationPool
     * @param Configuration $productConfiguration
     * @param ManagerInterface $messageManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     * @param ProductImageFactory $productImageFactory
     * @param ProductItemResolverInterface $productItemResolver
     */
    public function __construct(
        ConfigurationPool $configurationPool,
        Configuration $productConfiguration,
        ManagerInterface $messageManager,
        InterpretationStrategyInterface $messageInterpretationStrategy,
        ProductImageFactory $productImageFactory,
        ProductItemResolverInterface $productItemResolver
    ) {
        parent::__construct(
            $configurationPool,
            $productConfiguration,
            $messageManager,
            $messageInterpretationStrategy
        );
        $this->productImageFactory = $productImageFactory;
        $this->productItemResolver = $productItemResolver;
    }

    /**
     * Retrieve prepared product image html
     *
     * @param QuoteItem|CartItemInterface $quoteItem
     * @param string $imageType
     * @return string
     */
    public function getProductImageHtml($quoteItem, $imageType = 'cart_page_product_thumbnail')
    {
        /** @var Product $productForThumbnail */
        $productForThumbnail = $this->productItemResolver->getFinalProduct($quoteItem);
        $productImageBlock = $this->productImageFactory->create(
            $productForThumbnail,
            $imageType,
            []
        );
        $html = $productImageBlock->toHtml();
        return $html;
    }
}
