<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Observer\Reverse;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class to save product custom attribute Tax Product Posting Group
 */
class AssignProductPostTaxToProduct implements ObserverInterface
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

        $taxProductpostCode = $currentObj->dataHelper->getValueFromArray(
            "taxProductPostingGroupCode",
            $currentObj->stringData
        );
        if (isset($taxProductpostCode) && $taxProductpostCode !== null) {
            $currentObj->productInterface->setCustomAttribute("tax_product_posting_group", $taxProductpostCode);
        } else {
            $currentObj->productInterface->setCustomAttribute("tax_product_posting_group", '');
        }
    }
}
