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
namespace Aheadworks\Ctq\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Quote\Model\Quote as Quote;

/**
 * Class RedeemForOrder
 *
 * @package Aheadworks\Ctq\Observer
 */
class RedeemForOrder implements ObserverInterface
{
    /**
     * Convert quote data to order data
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var $order SalesOrder **/
        $order = $event->getOrder();
        /** @var $quote Quote */
        $quote = $event->getQuote();

        if ($quote->getAwCtqAmount()) {
            $order->setAwCtqAmount($quote->getAwCtqAmount());
            $order->setBaseAwCtqAmount($quote->getBaseAwCtqAmount());
        }
    }
}
