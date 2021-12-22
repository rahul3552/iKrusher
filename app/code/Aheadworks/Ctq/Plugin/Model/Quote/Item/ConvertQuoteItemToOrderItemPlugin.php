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
namespace Aheadworks\Ctq\Plugin\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Model\Order\Item;

/**
 * Class ConvertQuoteItemToOrderItemPlugin
 *
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Item
 */
class ConvertQuoteItemToOrderItemPlugin
{
    /**
     * Convert quote item data to order item data
     *
     * @param ToOrderItem $subject
     * @param callable $proceed
     * @param AbstractItem $item
     * @param array $additional
     * @return Item
     */
    public function aroundConvert(
        ToOrderItem $subject,
        callable $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);

        $orderItem->setAwCtqPercent($item->getAwCtqPercent());
        $orderItem->setAwCtqAmount($item->getAwCtqAmount());
        $orderItem->setBaseAwCtqAmount($item->getBaseAwCtqAmount());

        return $orderItem;
    }
}
