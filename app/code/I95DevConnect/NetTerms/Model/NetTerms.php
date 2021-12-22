<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class for define netterms model
 */
class NetTerms extends AbstractModel
{

    /**
     * Constructor to define model
     */
    protected function _construct()
    {
        $this->_init('I95DevConnect\NetTerms\Model\ResourceModel\NetTerms');
    }
}
