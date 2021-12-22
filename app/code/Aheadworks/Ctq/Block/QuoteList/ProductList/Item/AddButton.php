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
namespace Aheadworks\Ctq\Block\QuoteList\ProductList\Item;

use Magento\Catalog\Block\Product\ProductList\Item\Block;

/**
 * Class AddButton
 * @package Aheadworks\Ctq\Block\QuoteList\ProductList\Item
 */
class AddButton extends Block
{
    /**
     * Retrieve post params
     *
     * @return string
     */
    public function getAddToQuoteListParams()
    {
        $data = ['product' => $this->getProduct()->getId()];
        $url = $this->getUrl('aw_ctq/quoteList/add', ['_secure' => true]);

        return json_encode([
            'action' => $url,
            'data' => $data
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getProduct()
    {
        $product = parent::getProduct();

        return $product ?: $this->_coreRegistry->registry('product');
    }
}
