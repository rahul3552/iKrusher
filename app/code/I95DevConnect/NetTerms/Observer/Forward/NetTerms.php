<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Observer\Forward;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Forward customer netterms value to ERP
 */
class NetTerms implements ObserverInterface
{

    /**
     * Set i95Dev Custom attributes
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $currentObject = $observer->getEvent()->getData("customer");
        if (isset($currentObject->InfoData) && isset($currentObject->customer['custom_attributes'])) {
            foreach ($currentObject->customer['custom_attributes'] as $value) {
                if ($value['attribute_code'] == 'net_terms_id') {
                    $currentObject->InfoData['targetNetTermsId'] = $value['value'];
                }
            }
        }
    }
}
