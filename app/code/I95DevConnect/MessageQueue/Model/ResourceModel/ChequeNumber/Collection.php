<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ResourceModel\ChequeNumber;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * I95dev Cheque Number collection
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
            'I95DevConnect\MessageQueue\Model\ChequeNumber',
            'I95DevConnect\MessageQueue\Model\ResourceModel\ChequeNumber'
        );
    }
}
