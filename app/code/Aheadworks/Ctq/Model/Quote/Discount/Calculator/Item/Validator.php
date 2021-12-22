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
namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Aheadworks\Ctq\Model\Metadata\Negotiation\NegotiatedDiscountInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Item
 */
class Validator
{
    /**
     * Can apply discount on item
     *
     * @param AbstractItem $cartItem
     * @param NegotiatedDiscountInterface $negotiatedDiscount
     * @return bool
     */
    public function canApplyDiscount($cartItem, $negotiatedDiscount)
    {
        return is_numeric($negotiatedDiscount->getDiscountValue());
    }
}
