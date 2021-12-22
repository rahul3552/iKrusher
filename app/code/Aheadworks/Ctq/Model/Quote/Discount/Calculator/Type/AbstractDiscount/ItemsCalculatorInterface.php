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
namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount;

use Aheadworks\Ctq\Model\Metadata\Quote\Item\Discount as QuoteItemDiscount;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Aheadworks\Ctq\Model\Metadata\Negotiation\NegotiatedDiscountInterface;

/**
 * Interface ItemsCalculatorInterface
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator\Type\AbstractDiscount
 */
interface ItemsCalculatorInterface
{
    /**
     * Calculate item discount
     *
     * @param AbstractItem[] $cartItems
     * @param NegotiatedDiscountInterface $negotiatedDiscount
     * @return QuoteItemDiscount[]
     */
    public function calculate($cartItems, $negotiatedDiscount);
}
