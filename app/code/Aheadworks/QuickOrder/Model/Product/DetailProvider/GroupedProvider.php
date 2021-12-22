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

/**
 * Class GroupedProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class GroupedProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($productOption)
    {
        $selectedProducts = [];
        foreach ($this->subProducts as $product) {
            $selectedProducts[] = [
                'name' => $product->getName(),
                'qty' => $product->getCartQty()
            ];
        }

        return $selectedProducts;
    }

    /**
     * @inheritdoc
     */
    public function resolveAndSetSubProducts($products)
    {
        $this->subProducts = $products;
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
     */
    public function isQtyEditable()
    {
        return false;
    }
}
