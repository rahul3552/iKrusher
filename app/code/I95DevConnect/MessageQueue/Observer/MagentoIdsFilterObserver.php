<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class to filter MagentoIds
 */
class MagentoIdsFilterObserver implements ObserverInterface
{

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * MagentoIdsFilterObserver constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * get Magento ids
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $is_enabled = $this->dataHelper->isEnabled();
        if (!$is_enabled) {
            return;
        }
        $magentoMgObject = $observer->getEvent()->getData("magentoMgObject");
        $magentoMgObject->magentoIds = [];
    }
}
