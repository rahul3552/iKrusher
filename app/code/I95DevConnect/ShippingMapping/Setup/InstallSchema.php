<?php

/**
 * @author Arushi Bansal
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\ShippingMapping\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Interface for handling table creation during module install
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        $table = $setup->getConnection()->newTable($setup->getTable('i95dev_shipping_mapping_list'))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'id'
            )
            ->addColumn('erp_code', Table::TYPE_TEXT, '30', [], 'ERP Method Code')
            ->addColumn('magento_code', Table::TYPE_TEXT, '30', [], 'Magento Method Code')
            ->addColumn('is_ecommerce_default', Table::TYPE_SMALLINT, null, [], 'Is Ecommerce Default')
            ->addColumn('is_erp_default', Table::TYPE_SMALLINT, null, [], 'Is Erp Default');
        $setup->getConnection()->createTable($table);
    }
}
