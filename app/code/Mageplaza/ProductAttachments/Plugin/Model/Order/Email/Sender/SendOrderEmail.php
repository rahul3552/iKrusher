<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Plugin\Model\Order\Email\Sender;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Mageplaza\ProductAttachments\Helper\Data;

/**
 * Class SendOrderEmail
 * @package Mageplaza\ProductAttachments\Plugin\Model\Order\Email\Sender
 */
class SendOrderEmail
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * SendOrderEmail constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param OrderSender $subject
     * @param callable $proceed
     * @param Order $order
     * @param bool $forceSyncMode
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundSend(OrderSender $subject, callable $proceed, $order, $forceSyncMode = false)
    {
        $result = $proceed($order, $forceSyncMode);

        if ($order instanceof Order && $result && $this->helperData->isAllowAttachedFiles()) {
            $this->helperData->sendAttachedFiles($order);
        }

        return $result;
    }
}
