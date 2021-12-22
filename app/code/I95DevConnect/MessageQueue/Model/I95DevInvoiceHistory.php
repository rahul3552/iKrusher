<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * i95dev invoice history model
 * @api
 */
class I95DevInvoiceHistory extends AbstractModel
{

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('I95DevConnect\MessageQueue\Model\ResourceModel\I95DevInvoiceHistory');
    }
}
