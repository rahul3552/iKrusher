<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class to filter new magento ids
 */
class NewMagentoIdsFilterObserver implements ObserverInterface
{
    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * NewMagentoIdsFilterObserver constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * get Magento Ids
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $is_enabled = $this->dataHelper->isEnabled();
        if (!$is_enabled) {
            return;
        }

        $magentoMgObject = $observer->getEvent()->getData("magentoMgObject");
        $magentoMgObject->magentoIds = ["2", "3"];
    }
}
