<?php

/**
 * @author Arushi Bansal
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\ShippingMapping\Model\ResourceModel;

/**
 * shipping mapping resource model class
 */
class ShippingMapping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * shipping mapping table and id setting
     */
    protected function _construct()
    {
        $this->_init('i95dev_shipping_mapping_list', 'id');
    }

    /**
     * truncate table
     */
    public function truncateTable()
    {
        $this->getConnection()->truncateTable($this->getTable('i95dev_shipping_mapping_list'));
    }
}
