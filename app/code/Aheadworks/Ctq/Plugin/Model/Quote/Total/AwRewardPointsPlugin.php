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

/**
 * Class AwRewardPointsPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Total
 */
class AwRewardPointsPlugin extends AbstractResetTotalPlugin
{
    /**
     * @inheritdoc
     */
    protected function updateBeforeReset(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        $quote->setAwUseRewardPoints(false);
        foreach ($shippingAssignment->getItems() as $item) {
            $item->setAwRewardPointsAmount(0);
            $item->setBaseAwRewardPointsAmount(0);
            $item->setAwRewardPoints(0);

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setAwRewardPointsAmount(0);
                    $child->setBaseAwRewardPointsAmount(0);
                    $child->setAwRewardPoints(0);
                }
            }
        }
        return true;
    }
}
