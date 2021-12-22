<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Observer;

use Magento\Framework\Event\ObserverInterface;
use I95DevConnect\PriceLevel\Helper\Data;
use I95DevConnect\PriceLevel\Model\ItemPrice;

/**
 * Observer to get product price level price
 */
class BackFinalPriceObserver implements ObserverInterface
{

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $data;

    /**
     * Quote session object
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    public $quoteSession;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\ItemPrice
     */
    public $itemPrice;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param Data $data
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param ItemPrice $itemPrice
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Data $data,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        ItemPrice $itemPrice
    ) {

        $this->logger = $logger;
        $this->data = $data;
        $this->quoteSession = $quoteSession;
        $this->itemPrice = $itemPrice;
        $this->dataHelper = $dataHelper;
    }

    /**
     * set price level price to product
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->data->isEnabled()) {
            return $this;
        }
        $session = $this->quoteSession->getQuote();
        $customerId = $session->getCustomerId();
        $this->logger->debug($customerId);
        if ($customerId) {
            $event = $observer->getEvent();
            $product = $event->getProduct();
            $qty = $event->getQty();
            $actualFinalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
            if ($qty == '') {
                $qty = 1;
            }
            $this->logger->debug($product->getSku());
            $this->logger->debug($event->getQty());
            $finalPrice = $this->itemPrice->getItemFinalPrice($product, $customerId, $qty);
            $this->logger->debug($finalPrice);
            if ($finalPrice !== 0 && $actualFinalPrice > $finalPrice) {
                $product->setFinalPrice($finalPrice);
            }
            return $this;
        }
    }
}
