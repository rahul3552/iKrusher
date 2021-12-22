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

use Aheadworks\QuickOrder\Model\ProductList\Item\ProcessorInterface;
use Aheadworks\QuickOrder\Model\Product\Option\Converter as OptionConverter;

/**
 * Class ProductOption
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item\Processor
 */
class ProductOption implements ProcessorInterface
{
    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @param OptionConverter $optionConverter
     */
    public function __construct(
        OptionConverter $optionConverter
    ) {
        $this->optionConverter = $optionConverter;
    }

    /**
     * @inheritdoc
     */
    public function process($requestItem, $item, $product)
    {
        if (!$requestItem->getProductOption()) {
            if (!$item->getProductOption()) {
                $option = $this->optionConverter->toProductOptionObject($product->getTypeId(), []);
                $item->setProductOption($option);
            }
        } else {
            $item->setProductOption($requestItem->getProductOption());
        }
    }
}
