<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class NetTerms extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('i95dev_netterms', 'net_terms_id');
    }
}
