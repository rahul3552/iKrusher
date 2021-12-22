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

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class AwRafPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Total
 */
class AwRafPlugin extends AbstractResetTotalPlugin
{
    /**
     * @inheritdoc
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        $quote->setAwRafAmountType(null);
        foreach ($shippingAssignment->getItems() as $item) {
            /** @var QuoteItem $item */
            $item->setAwRafAmount(0);
            $item->setBaseAwRafAmount(0);
            $item->setAwRafPercent(0);
            $item->setAwRafRuleIds(null);
            $address = $item->getAddress();
            $address->setAwRafRuleIds(null);

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setAwRafAmount(0);
                    $child->setBaseAwRafAmount(0);
                }
            }
        }

        return true;
    }
}
