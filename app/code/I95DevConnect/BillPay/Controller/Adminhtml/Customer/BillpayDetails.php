<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Controller\Adminhtml\Customer;

/**
 * customer edit billpay detail controller class
 */
class BillpayDetails extends \Magento\Customer\Controller\Adminhtml\Index
{

    /**
     * @return object ResultPageFactory
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        return $this->resultLayoutFactory->create();
    }
}
