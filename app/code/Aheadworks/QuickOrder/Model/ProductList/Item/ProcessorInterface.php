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
namespace Aheadworks\QuickOrder\Model\ProductList\Item;

use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item
 */
interface ProcessorInterface
{
    /**
     * Prepare product list item using product and requested item
     *
     * @param ItemDataInterface $requestItem
     * @param ProductListItemInterface $item
     * @param ProductInterface $product
     */
    public function process($requestItem, $item, $product);
}
