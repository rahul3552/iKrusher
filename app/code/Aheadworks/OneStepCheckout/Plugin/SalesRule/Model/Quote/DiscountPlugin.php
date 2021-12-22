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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Plugin\SalesRule\Model\Quote;

use Magento\SalesRule\Model\Quote\Discount;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address;

/**
 * Class DiscountPlugin
 *
 * @package Aheadworks\OneStepCheckout\Plugin\SalesRule\Model\Quote
 */
class DiscountPlugin
{
    /**
     * @param Discount $discount
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCollect(
        Discount $discount,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        /** @var Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();
        if ($this->isNeedToUpdateShippingAmountForDiscount($address)) {
            $address->setShippingAmountForDiscount($address->getShippingDiscountAmount());
            $address->setBaseShippingAmountForDiscount($address->getBaseShippingDiscountAmount());
        }

        return [$quote, $shippingAssignment, $total];
    }

    /**
     * Check if need to update shipping amount for discount
     *
     * @param Address $address
     * @return bool
     */
    private function isNeedToUpdateShippingAmountForDiscount($address)
    {
        return (($address->getShippingAmountForDiscount() !== null)
            && (abs($address->getBaseShippingDiscountAmount()) > 0.0001));
    }
}
