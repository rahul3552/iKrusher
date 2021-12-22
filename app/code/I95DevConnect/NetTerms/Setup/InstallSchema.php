<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Interface for handling table creation during module install
 */
class InstallSchema implements InstallSchemaInterface
{
    const NULLABLE = 'nullable';

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        // Get i95dev_netterms_erp_report table
        $netTermstableName = $installer->getTable('i95dev_netterms');
        // Check if the table already exists
        // @codingStandardsIgnoreStart
        if (!$installer->getConnection()->isTableExists($netTermstableName)) {
            $netterms = $installer->getConnection()
                ->newTable($installer->getTable('i95dev_netterms'))
                ->addColumn(
                    'net_terms_id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, self::NULLABLE => false, 'primary' => true],
                    'Net Terms ID'
                )
                ->addColumn(
                    'target_net_terms_id',
                    Table::TYPE_TEXT,
                    null,
                    [self::NULLABLE => false],
                    'Net Terms Target ID'
                )
                ->addColumn('description', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Description')
                ->addColumn('due_type', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Due Type')
                ->addColumn(
                    'due_type_with_value',
                    Table::TYPE_TEXT,
                    null,
                    [self::NULLABLE => false],
                    'Due Type with Value'
                )
                ->addColumn('discount_type', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Discount Type')
                ->addColumn(
                    'discount_type_with_value',
                    Table::TYPE_TEXT,
                    null,
                    [self::NULLABLE => false],
                    'Discount Type with Value'
                )
                ->addColumn(
                    'discount_calculation_type',
                    Table::TYPE_TEXT,
                    null,
                    [self::NULLABLE => false],
                    'Discount Calculation Type'
                )
                ->addColumn('discount_percentage', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Discount Percentage')
                ->addColumn('discount_amount', Table::TYPE_SMALLINT, null, [self::NULLABLE => false], 'Discount Amount')
                ->addColumn('created_date', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Created Date')
                ->addColumn('updated_date', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Updated Date')
                ->addColumn('sale_or_purchase', Table::TYPE_TEXT, null, [self::NULLABLE => false], 'Sale or Purchase')
                ->setComment('I95dev Net Terms');
            // @codingStandardsIgnoreEnd
            $installer->getConnection()->createTable($netterms);
        }

        $tableName = $installer->getTable('i95dev_sales_flat_order');
        if ($installer->getConnection()->isTableExists($tableName)) {
                $connection = $installer->getConnection();

                $connection->addColumn(
                    $tableName,
                    'net_terms_id',
                    [
                        'type' => Table::TYPE_TEXT,
                        self::NULLABLE => false,
                        'default' => '',
                        'afters' => 'updated_at',
                        'comment'=>'netterms id'
                    ]
                );
        }

        $installer->endSetup();
    }
}
