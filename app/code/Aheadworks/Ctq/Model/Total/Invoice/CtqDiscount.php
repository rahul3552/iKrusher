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
namespace Aheadworks\Ctq\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Class CtqDiscount
 *
 * @package Aheadworks\Ctq\Model\Total\Invoice
 */
class CtqDiscount extends AbstractTotal
{
    /**
     * @inheritdoc
     */
    public function collect(Invoice $invoice)
    {
        $invoice->setAwCtqAmount(0);
        $invoice->setBaseAwCtqAmount(0);

        $order = $invoice->getOrder();
        if ($order->getBaseAwCtqAmount()
            && $order->getBaseAwCtqInvoiced() != $order->getBaseAwCtqAmount()
        ) {
            $totalAmount = 0;
            $baseTotalAmount = 0;

            /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemAmount = (double)$orderItem->getAwCtqAmount();
                $baseOrderItemAmount = (double)$orderItem->getBaseAwCtqAmount();
                $orderItemQty = $orderItem->getQtyOrdered();

                if ($orderItemAmount && $orderItemQty) {
                    // Resolve rounding problems
                    $amount = $orderItemAmount - $orderItem->getAwCtqInvoiced();
                    $baseAmount = $baseOrderItemAmount - $orderItem->getBaseAwCtqInvoiced();
                    if (!$item->isLast()) {
                        $activeQty = $orderItemQty - $orderItem->getQtyInvoiced();
                        $amount = $invoice->roundPrice(
                            $amount / $activeQty * $item->getQty(),
                            'regular',
                            true
                        );
                        $baseAmount = $invoice->roundPrice(
                            $baseAmount / $activeQty * $item->getQty(),
                            'base',
                            true
                        );
                    }

                    $item->setAwCtqAmount($amount);
                    $item->setBaseAwCtqAmount($baseAmount);

                    $orderItem->setAwCtqInvoiced(
                        $orderItem->getAwCtqInvoiced() + $item->getAwCtqAmount()
                    );
                    $orderItem->setBaseAwCtqInvoiced(
                        $orderItem->getBaseAwCtqInvoiced() + $item->getBaseAwCtqAmount()
                    );

                    $totalAmount += $amount;
                    $baseTotalAmount += $baseAmount;
                }
            }

            if ($baseTotalAmount > 0) {
                $invoice->setBaseAwCtqAmount(-$baseTotalAmount);
                $invoice->setAwCtqAmount(-$totalAmount);
            }

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalAmount);
        }
        return $this;
    }
}
