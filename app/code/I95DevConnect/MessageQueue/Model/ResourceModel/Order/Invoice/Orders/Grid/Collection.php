<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ResourceModel\Order\Invoice\Orders\Grid;

/**
 * Sales Invoice collection
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Invoice\Orders\Grid\Collection
{

    /**
     * Render additional filters and joins
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('i95dev_sales_flat_invoice');
        $this->getSelect()
            ->joinLeft(
                $joinTable.' as invoicetable',
                'main_table.increment_id = invoicetable.source_invoice_id',
                ['target_invoice_id']
            );
        parent::_renderFiltersBefore();
    }
}
