<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Observer\Forward;

use I95DevConnect\MessageQueue\Model\SalesOrderFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Order info is passed from Magento to ERP
 */
class OrderNetTerms implements ObserverInterface
{
    public $customSalesOrder;

    /**
     * Constructor
     * @param SalesOrderFactory $customSalesOrder
     */
    public function __construct(
        SalesOrderFactory $customSalesOrder
    ) {
         $this->customSalesOrder = $customSalesOrder;
    }

    /**
     * Assigning customer order netterm to current object
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $currentObject = $observer->getEvent()->getData("order");
        $loadCustomOrder = $this->customSalesOrder->create()
                ->load($currentObject->InfoData['sourceId'], 'source_order_id');

        if (isset($currentObject->InfoData)) {
            $currentObject->InfoData['netTermsId'] = $loadCustomOrder['net_terms_id'];
        }
    }
}
