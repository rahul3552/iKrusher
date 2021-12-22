<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\PaymentMapping\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Zend_Db_Exception;

/**
 * Interface for handling table creation during module install
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        $table = $setup->getConnection()->newTable($setup->getTable('i95dev_payment_mapping_list'))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'id'
            )
            ->addColumn('mapped_data', Table::TYPE_TEXT, '4M', [], 'ERP & Magento Payment Method Mapping Data')
            ->addColumn('created_at', Table::TYPE_DATETIME, null, ['nullable' => false], 'Creation Date');
        $setup->getConnection()->createTable($table);
    }
}
