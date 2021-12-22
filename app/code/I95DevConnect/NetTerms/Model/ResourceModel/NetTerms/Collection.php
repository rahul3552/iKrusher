<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model\ResourceModel\NetTerms;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of Netterms information
 */
class Collection extends AbstractCollection
{

    protected $_idFieldName = 'net_terms_id';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'I95DevConnect\NetTerms\Model\NetTerms',
            'I95DevConnect\NetTerms\Model\ResourceModel\NetTerms'
        );
    }
}
