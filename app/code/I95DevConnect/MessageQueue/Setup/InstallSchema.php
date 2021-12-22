<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Interface for handling table creation during module install
 */
class InstallSchema implements InstallSchemaInterface
{
    const NULLABLE = 'nullable';
    const I95DEV_CUSTOMER_GROUP = 'i95dev_customer_group';
    const IDENTITY = 'identity';
    const ERP_CODE = 'erp_code';
    const PRIMARY = 'primary';
    const UPDATEDBY = 'Updated By';
    const UPDATEDATETIME = 'Updated DateTime';
    const MSG_ID = 'msg_id';
    const ENTITY_ID = 'entity_id';
    const ENTITY_CODE = 'entity_code';
    const UPDATE_BY = 'update_by';
    const I95DEV_SALES_FLAT_ORDER = 'i95dev_sales_flat_order';
    const STATUS = 'status';
    const MAGENTO_ID = 'magento_id';
    const CREATED_DT = 'created_dt';
    const CREATIONDATE = 'Creation Date';
    const UPDATED_DT = 'updated_dt';

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     * @updatedBy Divya Koona. Removed gp_orderprocess_flag column from i95dev_sales_flat_order table
     */
    // @codingStandardsIgnoreFile
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable($installer->getTable(self::I95DEV_CUSTOMER_GROUP))
                ->addColumn('id', Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'id')
                ->addColumn('target_group_id', Table::TYPE_TEXT, '255', [], 'Target Group Id')
                ->addColumn('pricelevel_id', Table::TYPE_TEXT, '255', [], 'Price Level Id ')
                ->addColumn('customer_group_id', Table::TYPE_SMALLINT, null, [], 'Customer Group Id')
                ->addColumn('created_at', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Created Date')
                ->addColumn('updated_at', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Updated Date')
                ->addColumn(self::UPDATE_BY, Table::TYPE_TEXT, '255', [], self::UPDATEDBY)
                ->setComment(self::I95DEV_CUSTOMER_GROUP)
                ->addIndex(
                        $installer->getIdxName(
                        self::I95DEV_CUSTOMER_GROUP,
                        ['id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE),
                        ['id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    );
        $installer->getConnection()->createTable($table);
        $erpData = $installer->getConnection()
                ->newTable($installer->getTable('i95dev_erp_data'))
                ->addColumn('data_id', Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'Data Id')
                ->addColumn(self::MSG_ID, Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'Message Id')
                ->addColumn('data_string', Table::TYPE_TEXT, '4M', [], 'Data String')
                ->setComment('i95dev erp data');
        $installer->getConnection()->createTable($erpData);
        $entity = $installer->getConnection()->newTable($installer->getTable('i95dev_entity'))
                ->addColumn(self::ENTITY_ID, Table::TYPE_INTEGER, 11, [self::IDENTITY => true, self::NULLABLE => false, 'key' => true], 'Entity ID')
                ->addColumn('entity_name', Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Entity Name')
                ->addColumn('support_for_inbound', Table::TYPE_BOOLEAN, null, [self::NULLABLE => false], 'Support For Inbound')
                ->addColumn('support_for_outbound', Table::TYPE_BOOLEAN, null, [self::NULLABLE => false], 'Support For Outbound')
                ->addColumn(self::ENTITY_CODE, Table::TYPE_TEXT, 255, [self::IDENTITY => false, self::NULLABLE => false, self::PRIMARY => true], 'Entity Code')
                ->addColumn(self::CREATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::CREATIONDATE)
                ->addColumn(self::UPDATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::UPDATEDATETIME)
                ->addColumn('sort_order', Table::TYPE_DECIMAL, null, [self::NULLABLE => false], 'Sort Order')
                ->addIndex(
                    $installer->getIdxName('i95dev_entity', [self::ENTITY_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                    [self::ENTITY_ID],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('Entity Details');
        $installer->getConnection()->createTable($entity);
        $erpMessageQueue = $installer->getConnection()->newTable($installer->getTable('i95dev_erp_messagequeue'))
                ->addColumn(self::MSG_ID, Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'MSG ID')
                ->addColumn(self::ERP_CODE, Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Erp Code')
                ->addColumn(self::ENTITY_CODE, Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Entity Type Code')
                ->addColumn(self::CREATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::CREATIONDATE)
                ->addColumn(self::UPDATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::UPDATEDATETIME)
                ->addColumn(self::STATUS, Table::TYPE_SMALLINT, null, [self::NULLABLE => false], self::STATUS)
                ->addColumn(self::MAGENTO_ID, Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'magento id')
                ->addColumn('target_id', Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Target_Id')
                ->addColumn('error_id', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'error id')
                ->addColumn('counter', Table::TYPE_SMALLINT, null, [self::NULLABLE => false], 'counter')
                ->addColumn('ref_name', Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'ref name')
                ->addColumn('is_data_error', Table::TYPE_SMALLINT, null, [self::NULLABLE => false], 'Is Data Error')
                ->addColumn('parent_msg_id', Table::TYPE_INTEGER, null, [self::NULLABLE => false], 'Parent Msg Id')
                ->addColumn('destination_msg_id', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'Erp Msg id')
                ->addIndex(
                        $installer->getIdxName('i95dev_erp_messagequeue', [self::ERP_CODE, self::ENTITY_CODE, self::MAGENTO_ID], AdapterInterface::INDEX_TYPE_INDEX),
                        [self::ERP_CODE, self::ENTITY_CODE, self::MAGENTO_ID],
                        ['type' => AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->setComment('Erp Messagequeue Details');
        $installer->getConnection()->createTable($erpMessageQueue);
        $erpMessageQueue = $installer->getConnection()->newTable($installer->getTable('i95dev_magento_messagequeue'))
                ->addColumn(self::MSG_ID, Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'MSG ID')
                ->addColumn(self::ERP_CODE, Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Erp Code')
                ->addColumn(self::ENTITY_CODE, Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Entity Type Code')
                ->addColumn(self::CREATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::CREATIONDATE)
                ->addColumn(self::UPDATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::UPDATEDATETIME)
                ->addColumn(self::STATUS, Table::TYPE_SMALLINT, null, [self::NULLABLE => false], self::STATUS)
                ->addColumn(self::MAGENTO_ID, Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'magento id')
                ->addColumn('target_id', Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'Target_Id')
                ->addColumn('updated_by', Table::TYPE_TEXT, 255, [self::NULLABLE => false], self::UPDATEDBY)
                ->addColumn('error_id', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'Error Id')
                ->addColumn('destination_msg_id', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'Erp Msg id')
                ->addIndex(
                    $installer->getIdxName('i95dev_magento_messagequeue', [self::ERP_CODE, self::ENTITY_CODE, self::MAGENTO_ID], AdapterInterface::INDEX_TYPE_INDEX),
                    [self::ERP_CODE, self::ENTITY_CODE, self::MAGENTO_ID],
                    ['type' => AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->setComment('Magento Messagequeue Details');
        $installer->getConnection()->createTable($erpMessageQueue);
        $error = $installer->getConnection()->newTable($installer->getTable('i95dev_error_report'))
                ->addColumn('id', Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'Id')
                ->addColumn('msg', Table::TYPE_TEXT, 255, [self::NULLABLE => false], 'msg')
                ->setComment('i95dev_error_report');
        $installer->getConnection()->createTable($error);
        $table = $installer->getConnection()->newTable($installer->getTable(self::I95DEV_SALES_FLAT_ORDER))
                ->addColumn('id', Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'id')
                ->addColumn('target_order_id', Table::TYPE_TEXT, '255', [], 'Target Order Id')
                ->addColumn('source_order_id', Table::TYPE_TEXT, '255', [], 'Target Order Id ')
                ->addColumn('target_order_status', Table::TYPE_TEXT, '255', [], 'Target Order Status ')
                ->addColumn('origin', Table::TYPE_TEXT, '255', [], 'Origin')
                ->addColumn('additional_info', Table::TYPE_TEXT, '1024MB', [], 'Additional Info ')
                ->addColumn('created_at', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Created Date')
                ->addColumn('updated_at', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Updated Date')
                ->addColumn(self::UPDATE_BY, Table::TYPE_TEXT, '255', [], self::UPDATEDBY)
                ->setComment(self::I95DEV_SALES_FLAT_ORDER)
                ->addIndex($installer->getIdxName(self::I95DEV_SALES_FLAT_ORDER, ['id'], AdapterInterface::INDEX_TYPE_UNIQUE), ['id'], ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]);
        $installer->getConnection()->createTable($table);
        $table = $installer->getConnection()->newTable($installer->getTable('i95dev_sales_flat_order_payment'))
                ->addColumn('id', Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'id')
                ->addColumn('target_order_id', Table::TYPE_TEXT, '255', [], 'Target Order Id')
                ->addColumn('target_cheque_number', Table::TYPE_TEXT, '255', [], 'Ttarget Cheque Number')
                ->addColumn('source_order_id', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'Source Order Id')
                ->setComment('i95dev_sales_flat_order_payment');
        $installer->getConnection()->createTable($table);
        $sales_flat_invoice = $installer->getConnection()->newTable($installer->getTable('i95dev_sales_flat_invoice'))
                ->addColumn('id', Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'Id')
                ->addColumn('target_invoice_id', Table::TYPE_TEXT, '255', [self::NULLABLE => false], 'Target Invoice Id')
                ->addColumn('source_invoice_id', Table::TYPE_TEXT, '255', [self::NULLABLE => false], 'Source Invoice Id')
                ->addColumn(self::CREATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::CREATIONDATE)
                ->addColumn(self::UPDATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::UPDATEDATETIME)
                ->addColumn(self::UPDATE_BY, Table::TYPE_TEXT, 255, [self::NULLABLE => false], self::UPDATEDBY)
                ->addColumn(
                    'target_invoiced_qty',
                    Table::TYPE_SMALLINT, null,
                    [self::NULLABLE => false, 'unsigned' => true, 'default' => 0],
                    'Target Invoiced Qty'
                )
                ->setComment('i95dev sales flat invoice');
        $installer->getConnection()->createTable($sales_flat_invoice);
        $sales_flat_shipment = $installer->getConnection()->newTable($installer->getTable('i95dev_sales_flat_shipment'))
                ->addColumn('id', Table::TYPE_BIGINT, null, [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true], 'Id')
                ->addColumn('target_shipment_id', Table::TYPE_TEXT, '255', [self::NULLABLE => false], 'Target Shipment Id')
                ->addColumn('source_shipment_id', Table::TYPE_TEXT, '255', [self::NULLABLE => false], 'Source Shipment Id')
                ->addColumn(self::CREATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::CREATIONDATE)
                ->addColumn(self::UPDATED_DT, Table::TYPE_DATETIME, null, [self::NULLABLE => false], self::UPDATEDATETIME)
                ->addColumn(self::UPDATE_BY, Table::TYPE_TEXT, 255, [self::NULLABLE => false], self::UPDATEDBY)
                ->setComment('i95dev sales flat shipment');
        $installer->getConnection()->createTable($sales_flat_shipment);
        $installer->endSetup();
    }
}
