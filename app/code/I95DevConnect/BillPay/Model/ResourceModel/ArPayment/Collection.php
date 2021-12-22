<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\ResourceModel\ArPayment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'I95DevConnect\BillPay\Model\ArPayment',
            'I95DevConnect\BillPay\Model\ResourceModel\ArPayment'
        );
    }
}
