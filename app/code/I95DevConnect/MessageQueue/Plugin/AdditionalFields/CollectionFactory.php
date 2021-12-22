<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Plugin\AdditionalFields;

/**
 * Grid Collection
 */
class CollectionFactory extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    protected function _initSelect()
    {
        $this->addFilterToMap('created_at', 'main_table.created_at');
        parent::_initSelect();
    }

    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('i95dev_sales_flat_order');
        $this->getSelect()
            ->joinLeft(
                $joinTable.' as ordertable',
                'main_table.increment_id = ordertable.source_order_id',
                ['target_order_id', 'origin']
            );
        parent::_renderFiltersBefore();
    }
}
