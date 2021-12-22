<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Model class for Custom Billing process
 */
class Arbook extends AbstractModel
{

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('I95DevConnect\BillPay\Model\ResourceModel\Arbook');
    }
}
