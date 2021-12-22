<?php
/**
 * Send email to customer on order cancel
 *
 * @package   I95DevConnect_CancelOrder
 * @author    i95Dev Team <info@i95dev.com>
 * @copyright Copyright (c) 2021 i95Dev(https://www.i95dev.com)
 */

namespace I95DevConnect\CancelOrder\Observer;

use I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\MessageQueue\Model\Logger;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class OrderSaveAfter implements ObserverInterface
{

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender
     */
    protected $commentEmailSender;
    public $logger;

    /**
     * OrderSaveAfter constructor.
     *
     * @param OrderCommentSender $commentEmailSender
     */
    public function __construct(
        OrderCommentSender $commentEmailSender,
        Logger $logger
    ) {
        $this->commentEmailSender = $commentEmailSender;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() === 'canceled') {
            try {
                $this->commentEmailSender->send($order, true);
            } catch (LocalizedException $ex) {
                $this->logger->createLog(
                    __METHOD__,
                    $ex->getMessage(),
                    Data::I95EXC,
                    'critical'
                );

                return $ex->getMessage();
            }
        }
    }
}
