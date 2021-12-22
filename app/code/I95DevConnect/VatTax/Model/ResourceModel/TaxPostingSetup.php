<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model class for Tax Posting Setup
 */
class TaxPostingSetup extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('i95dev_tax_postingsetup', 'id');
    }
}
