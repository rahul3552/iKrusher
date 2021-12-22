<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class for sales quote submit
 */
class SalesQuoteSubmitBefore implements ObserverInterface
{

    const I95EXC = 'i95devApiException';
    public $dataHelper;

    /**
     * @var \Magento\CatalogInventory\Observer\ItemsForReindex
     */
    public $itemsForReindex;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\CatalogInventory\Observer\ItemsForReindex $itemsForReindex
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\CatalogInventory\Observer\ItemsForReindex $itemsForReindex
    ) {
        $this->dataHelper = $dataHelper;
        $this->itemsForReindex = $itemsForReindex;
    }

    /**
     * Save i95Dev Custom attributes.
     * @param  \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $is_enabled = $this->dataHelper->isEnabled();
            if (!$is_enabled) {
                return;
            }
            if ($this->dataHelper->getGlobalValue('i95_observer_skip')) {
                $quote = $observer->getQuote();
                $quote->setInventoryProcessed(true);
                $this->itemsForReindex->setItems([]);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->dataHelper->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
    }
}
