<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Arbook extends AbstractDb
{

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('i95dev_ar_book', 'primary_id');
    }
}
