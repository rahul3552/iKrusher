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
namespace Aheadworks\Ctq\Block\QuoteList\Item\Renderer\Actions;

use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;

/**
 * Class Edit
 * @package Aheadworks\Ctq\Block\Cart\Item\Renderer\Actions
 */
class Edit extends Generic
{
    /**
     * Get quote list item configure url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl(
            'checkout/cart/configure',
            [
                'id' => $this->getItem()->getId(),
                'product_id' => $this->getItem()->getProduct()->getId(),
                Checker::AW_CTQ_QUOTE_LIST_FLAG => '1'
            ]
        );
    }
}
