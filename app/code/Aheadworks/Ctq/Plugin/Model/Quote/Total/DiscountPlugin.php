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
namespace Aheadworks\Ctq\Plugin\Model\Quote\Total;

use Aheadworks\Ctq\Model\QuoteList\Checker;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\SalesRule\Model\Quote\Discount;

/**
 * Class DiscountPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Total
 */
class DiscountPlugin extends AbstractResetTotalPlugin
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Checker $checker
     */
    public function __construct(
        Checker $checker
    ) {
        $this->checker = $checker;
    }

    /**
     * @inheritdoc
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        foreach ($shippingAssignment->getItems() as $item) {
            $item->setNoDiscount(1);
            $item->setDiscountAmount(0);
            $item->setBaseDiscountAmount(0);

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setDiscountAmount(0);
                    $child->setBaseDiscountAmount(0);
                }
            }
        }

        if ($quote->getCouponCode()) {
            $this->discountUsed = true;
        }

        return true;
    }

    /**
     * Don't calculate discount at Quote List page
     *
     * @param Discount $subject
     * @param \Closure $proceed
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return Discount
     */
    public function aroundCollect(
        Discount $subject,
        \Closure $proceed,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        return $this->checker->isAwCtqQuote($quote)
            ? $subject
            : $proceed($quote, $shippingAssignment, $total);
    }
}
