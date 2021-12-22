<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * I95Dev Invoice Item History resource model
 */
class I95DevInvoiceItemHistory extends AbstractDb
{

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('i95dev_sales_invoice_item_history', 'id');
    }
}
