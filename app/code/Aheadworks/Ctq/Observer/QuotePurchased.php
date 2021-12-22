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

use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QuotePurchased
 * @package Aheadworks\Ctq\Observer
 */
class QuotePurchased implements ObserverInterface
{
    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @param QuoteManagement $quoteManagement
     */
    public function __construct(QuoteManagement $quoteManagement)
    {
        $this->quoteManagement = $quoteManagement;
    }

    /**
     * Set quote status ordered
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var $order \Magento\Sales\Model\Order **/
        $order = $event->getOrder();
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $event->getQuote();

        $this->quoteManagement->quotePurchased($quote, $order);
    }
}
