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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class QuoteSubmitBeforeObserver
 * @package Aheadworks\OneStepCheckout\Observer
 */
class QuoteSubmitBeforeObserver implements ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var OrderInterface $order */
        $order = $event->getOrder();
        /** @var Quote $quote */
        $quote = $event->getQuote();

        $order->setAwOrderNote($quote->getAwOrderNote());
        $order->setAwDeliveryDate($quote->getAwDeliveryDate());
        $order->setAwDeliveryDateFrom($quote->getAwDeliveryDateFrom());
        $order->setAwDeliveryDateTo($quote->getAwDeliveryDateTo());

        return $this;
    }
}
