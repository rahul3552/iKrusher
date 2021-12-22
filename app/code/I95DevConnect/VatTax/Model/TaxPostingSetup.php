<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Model class for Tax Posting Setup
 */
class TaxPostingSetup extends AbstractModel
{

    /**
     * Define resource model
     * */
    protected function _construct()
    {
        $this->_init('I95DevConnect\VatTax\Model\ResourceModel\TaxPostingSetup');
    }
}
