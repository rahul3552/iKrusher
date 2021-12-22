<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const LENGTH='length';
    const NULLABLE = 'nullable';
    const TARGETINVOICEID = 'target_invoice_id';
    const TARGETORDERID = 'target_order_id';

    /**
     * {}
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.1.6', '<')) {
            $tableName = $setup->getTable('i95dev_entity');
            if ($setup->getConnection()->isTableExists($tableName)) {
                $connection = $setup->getConnection();
                $connection->changeColumn(
                    $tableName,
                    'sort_order',
                    'sort_order',
                    ['type' => Table::TYPE_DECIMAL, self::NULLABLE => false, 'default' => 0, self::LENGTH => '10,1'],
                    'Sort Order'
                );
            }

            $i95devSalesFlatInvoice = $setup->getTable('i95dev_sales_flat_invoice');
            if ($setup->getConnection()->isTableExists($i95devSalesFlatInvoice)) {
                $connection = $setup->getConnection();
                $connection->addIndex(
                    $i95devSalesFlatInvoice,
                    $setup->getIdxName($i95devSalesFlatInvoice, 'source_invoice_id'),
                    ['source_invoice_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
                $connection->addIndex(
                    $i95devSalesFlatInvoice,
                    $setup->getIdxName($i95devSalesFlatInvoice, self::TARGETINVOICEID),
                    [self::TARGETINVOICEID],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
            }

            $i95devSalesFlatOrder = $setup->getTable('i95dev_sales_flat_order');
            if ($setup->getConnection()->isTableExists($i95devSalesFlatOrder)) {
                $connection = $setup->getConnection();
                $connection->addIndex(
                    $i95devSalesFlatOrder,
                    $setup->getIdxName($i95devSalesFlatOrder, 'source_order_id'),
                    ['source_order_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
                $connection->addIndex(
                    $i95devSalesFlatOrder,
                    $setup->getIdxName($i95devSalesFlatOrder, self::TARGETORDERID),
                    [self::TARGETORDERID],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
            }

            $i95devSalesFlatShipment = $setup->getTable('i95dev_sales_flat_shipment');
            if ($setup->getConnection()->isTableExists($i95devSalesFlatShipment)) {
                $connection = $setup->getConnection();
                $connection->addIndex(
                    $i95devSalesFlatShipment,
                    $setup->getIdxName($i95devSalesFlatShipment, 'target_shipment_id'),
                    ['target_shipment_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                );

                $connection->addIndex(
                    $i95devSalesFlatShipment,
                    $setup->getIdxName($i95devSalesFlatShipment, 'source_shipment_id'),
                    ['source_shipment_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
            }

            $i95devErpMQ = $setup->getTable('i95dev_erp_messagequeue');
            if ($setup->getConnection()->isTableExists($i95devErpMQ)) {
                $connection = $setup->getConnection();
                $indexColumnArr = [
                    'status',
                    'target_id',
                    'error_id',
                    'counter',
                    'parent_msg_id',
                    'destination_msg_id'
                ];

                foreach ($indexColumnArr as $column) {
                    $connection->addIndex(
                        $i95devErpMQ,
                        $setup->getIdxName($i95devErpMQ, $column),
                        [$column],
                        AdapterInterface::INDEX_TYPE_INDEX
                    );
                }
            }

            $this->addAdditionalInfo($setup);

            $this->createInvoiceHistoryTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param $setup
     */
    public function createInvoiceHistoryTable($setup)
    {
        $invoice_history = $setup->getConnection()->newTable($setup->getTable('i95dev_sales_invoice_history'))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, self::NULLABLE => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                self::TARGETINVOICEID,
                Table::TYPE_TEXT,
                null,
                [self::NULLABLE => false],
                'Target Invoice Id'
            )
            ->addColumn(self::TARGETORDERID, Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Target Order Id')
            ->setComment('i95dev invoice history to track partial invoices done to captured payments');

        $setup->getConnection()->createTable($invoice_history);

        $invoice_item_history = $setup->getConnection()->newTable(
            $setup->getTable('i95dev_sales_invoice_item_history')
        )
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, self::NULLABLE => false, 'primary' => true],
                'ID'
            )
            ->addColumn('invoice_entity_id', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Invoice Entity ID')
            ->addColumn('item_sku', Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Item sku')
            ->addColumn('item_qty', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'item qty')
            ->setComment(
                'i95dev invoice item history to track items of partial invoices done to captured payments'
            );

        $setup->getConnection()->createTable($invoice_item_history);
    }

    /**
     * @param $setup
     */
    public function addAdditionalInfo($setup)
    {
        $tableName = $setup->getTable('i95dev_magento_messagequeue');
        if ($setup->getConnection()->isTableExists($tableName)) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $tableName,
                'additional_info',
                Table::TYPE_TEXT,
                null,
                [self::LENGTH => '2M'],
                'Additional Info'
            );
        }

        $tableName = $setup->getTable('i95dev_erp_messagequeue');
        if ($setup->getConnection()->isTableExists($tableName)) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $tableName,
                'additional_info',
                Table::TYPE_TEXT,
                null,
                [self::LENGTH => '2M'],
                'Additional Info'
            );
        }
    }
}
