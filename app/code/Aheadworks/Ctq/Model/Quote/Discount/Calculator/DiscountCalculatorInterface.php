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
namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator;

use Aheadworks\Ctq\Model\Metadata\Quote\Discount as QuoteDiscount;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Api\Data\AddressInterface;
use Aheadworks\Ctq\Model\Metadata\Negotiation\NegotiatedDiscountInterface;

/**
 * Interface DiscountCalculatorInterface
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator
 */
interface DiscountCalculatorInterface
{
    /**
     * Discount calculate types
     */
    const CALCULATE_RESET = 2;
    const CALCULATE_PER_ITEM = 1;
    const CALCULATE_DEFAULT = 0;

    /**
     * Calculate discount
     *
     * @param AbstractItem[] $cartItems
     * @param AddressInterface $address
     * @param NegotiatedDiscountInterface $negotiatedDiscount
     * @return QuoteDiscount
     */
    public function calculate($cartItems, $address, $negotiatedDiscount);
}
