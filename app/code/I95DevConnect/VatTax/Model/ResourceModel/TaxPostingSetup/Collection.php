<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\ResourceModel\TaxPostingSetup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class for tax posting Collection
 */
class Collection extends AbstractCollection
{
    /**
     * vattax collection model constructor
     */
    protected function _construct()
    {
        $this->_init(
            'I95DevConnect\VatTax\Model\TaxPostingSetup',
            'I95DevConnect\VatTax\Model\ResourceModel\TaxPostingSetup'
        );
    }
}
