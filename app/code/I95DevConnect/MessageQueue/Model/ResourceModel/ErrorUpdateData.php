<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * ErrorUpdateData resource model
 */
class ErrorUpdateData extends AbstractDb
{

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init('i95dev_error_report', 'id');
    }
}
