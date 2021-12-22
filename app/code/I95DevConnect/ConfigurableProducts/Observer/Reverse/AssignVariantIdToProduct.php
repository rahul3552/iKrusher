<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Observer\Reverse;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class to save product custom attribute variantId
 * @author Hrusikesh Manna
 */
class AssignVariantIdToProduct implements ObserverInterface
{
    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * AssignVariantIdToProduct constructor.
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }
    
    /**
     * save i95Dev custom attribute
     * @param \Magento\Framework\Event\Observer $observer
     * @autor Hrusieksh Manna
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $component = $this->dataHelper->getComponent();
        if ($component == 'AX' || $component == 'D365FO') {
            $currentObj = $observer->getEvent()->getData("currentObject");
            $variantId = $currentObj->dataHelper->getValueFromArray("variantId", $currentObj->stringData);
            if (isset($variantId)) {
                $currentObj->productInterface->setCustomAttribute("variant_id", $variantId);
            }
        }
    }
}
