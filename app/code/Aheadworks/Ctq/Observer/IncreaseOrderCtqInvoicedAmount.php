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
 * Class IncreaseOrderCtqInvoicedAmount
 *
 * @package Aheadworks\Ctq\Observer
 */
class IncreaseOrderCtqInvoicedAmount implements ObserverInterface
{
    /**
     * Increase order aw_ctq_invoiced attribute based on created invoice
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getBaseAwCtqAmount()) {
            $order->setBaseAwCtqInvoiced(
                $order->getBaseAwCtqInvoiced() + $invoice->getBaseAwCtqAmount()
            );
            $order->setAwCtqInvoiced(
                $order->getAwCtqInvoiced() + $invoice->getAwCtqAmount()
            );
        }
        return $this;
    }
}
