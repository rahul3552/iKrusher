<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\ResourceModel\TaxProductPostingGroups;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection for tax product posting group
 */
class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            'I95DevConnect\VatTax\Model\TaxProductPostingGroups',
            'I95DevConnect\VatTax\Model\ResourceModel\TaxProductPostingGroups'
        );
    }
}
