<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Model class for Tax Business Posting Groups
 */
class TaxBusPostingGroups extends AbstractModel
{

    /**
     * Define resource model
     * */
    protected function _construct()
    {
        $this->_init('I95DevConnect\VatTax\Model\ResourceModel\TaxBusPostingGroups');
    }
}
