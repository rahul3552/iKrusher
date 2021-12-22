<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Observer\Reverse;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class to save customer custom attribute Tax Business Posting Group
 */
class AssignBusPostTaxToCustomer implements ObserverInterface
{

    /**
     * Save i95Dev Custom attributes
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currentObj = $observer->getEvent()->getData("currentObject");
        $taxBuspostCode = $currentObj->dataHelper->getValueFromArray("taxBusPostingGroupCode", $currentObj->stringData);
        if (isset($taxBuspostCode) && $taxBuspostCode !== null) {
            $currentObj->customerInterface->setCustomAttribute('tax_bus_posting_group', $taxBuspostCode);
        } else {
            $currentObj->customerInterface->setCustomAttribute('tax_bus_posting_group', '');
        }
    }
}
