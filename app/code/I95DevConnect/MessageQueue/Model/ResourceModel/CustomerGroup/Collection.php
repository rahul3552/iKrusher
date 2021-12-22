<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ResourceModel\CustomerGroup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * i95dev Customer Group collection
 */
class Collection extends AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'I95DevConnect\MessageQueue\Model\CustomerGroup',
            'I95DevConnect\MessageQueue\Model\ResourceModel\CustomerGroup'
        );
    }
}
