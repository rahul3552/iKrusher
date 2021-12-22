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

/**
 * Class IncreaseOrderCtqRefundedAmount
 *
 * @package Aheadworks\Ctq\Observer
 */
class IncreaseOrderCtqRefundedAmount implements ObserverInterface
{
    /**
     * Increase order aw_ctq_refunded attribute based on created creditmemo
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseAwCtqAmount()) {
            $order->setBaseAwCtqRefunded($order->getBaseAwCtqRefunded() + $creditmemo->getBaseAwCtqAmount());
            $order->setAwCtqRefunded($order->getAwCtqRefunded() + $creditmemo->getAwCtqAmount());
        }

        return $this;
    }
}
