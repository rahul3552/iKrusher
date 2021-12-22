<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\ResourceModel\TaxBusPostingGroups;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection for tax bus posting group
 */
class Collection extends AbstractCollection
{

    /**
     * function to identify the resource model for tax bus posting group
     */
    protected function _construct()
    {
        $this->_init(
            'I95DevConnect\VatTax\Model\TaxBusPostingGroups',
            'I95DevConnect\VatTax\Model\ResourceModel\TaxBusPostingGroups'
        );
    }
}
