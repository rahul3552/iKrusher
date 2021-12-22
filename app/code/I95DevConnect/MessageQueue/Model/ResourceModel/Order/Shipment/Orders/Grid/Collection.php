<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ResourceModel\Order\Shipment\Orders\Grid;

/**
 * Collection class for order grid
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Shipment\Order\Grid\Collection
{
    /**
     * Render additional filters and joins
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('i95dev_sales_flat_shipment');
        $this->getSelect()
            ->joinLeft(
                $joinTable.' as shipmenttable',
                'main_table.increment_id = shipmenttable.source_shipment_id',
                ['target_shipment_id']
            );
        parent::_renderFiltersBefore();
    }
}
