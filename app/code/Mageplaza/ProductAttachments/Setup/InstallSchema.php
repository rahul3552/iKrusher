<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\ProductAttachments\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('mageplaza_productattachments_file')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_productattachments_file'))
                ->addColumn('file_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ], 'File Id')
                ->addColumn('label', Table::TYPE_TEXT, 255, [], 'File Label')
                ->addColumn('name', Table::TYPE_TEXT, 255, [], 'File Name')
                ->addColumn('status', Table::TYPE_SMALLINT, 1, [], 'Status')
                ->addColumn('store_ids', Table::TYPE_TEXT, null, ['nullable' => false, 'unsigned' => true], 'Store Ids')
                ->addColumn(
                    'customer_group',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer Group'
                )
                ->addColumn('size', Table::TYPE_INTEGER, null, [], 'File Size')
                ->addColumn('file_path', Table::TYPE_TEXT, 255, [], 'File Path')
                ->addColumn('file_icon_path', Table::TYPE_TEXT, '2M', [], 'File Icon Path')
                ->addColumn('customer_login', Table::TYPE_SMALLINT, 1, [], 'Is Customer Login')
                ->addColumn('is_buyer', Table::TYPE_SMALLINT, 1, [], 'Is Buyer')
                ->addColumn('file_action', Table::TYPE_SMALLINT, 1, [], 'File Action')
                ->addColumn(
                    'priority',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Priority'
                )
                ->addColumn('position', Table::TYPE_INTEGER, null, [], 'File Position')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'File Created At')
                ->addColumn('is_grid', Table::TYPE_SMALLINT, 1, [], 'Is Grid')
                ->addColumn('conditions_serialized', Table::TYPE_TEXT, '2M', [], 'Conditions Serialized')
                ->setComment('Product Attachment File');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_productattachments_log')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_productattachments_log'))
                ->addColumn('log_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ], 'File Log Id')
                ->addColumn('file_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false,], 'File ID')
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false,],
                    'User Download ID'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false,],
                    'Product ID'
                )
                ->addColumn('file_action', Table::TYPE_SMALLINT, 1, [], 'File Action')
                ->addColumn('store_id', Table::TYPE_TEXT, null, ['nullable' => false, 'unsigned' => true], 'Store Id')
                ->addColumn(
                    'customer_group',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer Group'
                )
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'File Created At')
                ->addIndex($installer->getIdxName('mageplaza_productattachments_log', ['log_id']), ['log_id'])
                ->addIndex($installer->getIdxName('mageplaza_productattachments_log', ['file_id']), ['file_id'])
                ->addIndex($installer->getIdxName('mageplaza_productattachments_log', ['customer_id']), ['customer_id'])
                ->addIndex($installer->getIdxName('mageplaza_productattachments_log', ['product_id']), ['product_id'])
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_productattachments_log',
                        'file_id',
                        'mageplaza_productattachments_file',
                        'file_id'
                    ),
                    'file_id',
                    $installer->getTable('mageplaza_productattachments_file'),
                    'file_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_productattachments_log',
                        'product_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Product Attachment Log');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_productattachments_file_product')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_productattachments_file_product'))
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false
                ], 'Entity ID')
                ->addColumn('file_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false
                ], 'File ID')
                ->addIndex(
                    $installer->getIdxName('mageplaza_productattachments_file_product', ['entity_id']),
                    ['entity_id']
                )
                ->addIndex(
                    $installer->getIdxName('mageplaza_productattachments_file_product', ['file_id']),
                    ['file_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_productattachments_file_product',
                        'entity_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'entity_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_productattachments_file_product',
                        'file_id',
                        'mageplaza_productattachments_file',
                        'file_id'
                    ),
                    'file_id',
                    $installer->getTable('mageplaza_productattachments_file'),
                    'file_id',
                    Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName(
                        'mageplaza_productattachments_file_product',
                        ['entity_id', 'file_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['entity_id', 'file_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('File To Product Link Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
