<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Setup;

use Aheadworks\OneStepCheckout\Model\Report\Aggregation;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Aheadworks\OneStepCheckout\Setup
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**#@+
     * Connection names for split database solutions
     */
    const CHECKOUT_CONNECTION_NAME = 'checkout';
    const SALES_CONNECTION_NAME = 'sales';
    /**#@-*/

    /**
     * @var Aggregation
     */
    private $aggregation;

    /**
     * @param Aggregation $aggregation
     */
    public function __construct(Aggregation $aggregation)
    {
        $this->aggregation = $aggregation;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Create table 'aw_osc_report_abandoned_checkouts_index'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_osc_report_abandoned_checkouts_index'))
            ->addColumn(
                'index_id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Index Id'
            )->addColumn(
                'period',
                Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'abandoned_checkouts_count',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Abandoned Checkouts Count'
            )->addColumn(
                'abandoned_checkouts_revenue',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Abandoned Checkouts Revenue'
            )->addColumn(
                'completed_checkouts_count',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Completed Checkouts Count'
            )->addColumn(
                'completed_checkouts_revenue',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Completed Checkouts Revenue'
            )->addColumn(
                'conversion',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Conversion'
            )->addColumn(
                'base_to_global_rate',
                Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Base To Global Rate'
            )->addColumn(
                'base_to_global_rate',
                Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Base To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_osc_report_abandoned_checkouts_index', ['period']),
                ['period']
            )->addIndex(
                $installer->getIdxName('aw_osc_report_abandoned_checkouts_index', ['store_id']),
                ['store_id']
            )->addIndex(
                $installer->getIdxName('aw_osc_report_abandoned_checkouts_index', ['customer_group_id']),
                ['customer_group_id']
            )->addForeignKey(
                $installer->getFkName('aw_osc_report_abandoned_checkouts_index', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Abandoned Checkouts Index Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_osc_report_abandoned_checkouts_index_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_osc_report_abandoned_checkouts_index_idx'))
            ->addColumn(
                'index_id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Index Id'
            )->addColumn(
                'period',
                Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'abandoned_checkouts_count',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Abandoned Checkouts Count'
            )->addColumn(
                'abandoned_checkouts_revenue',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Abandoned Checkouts Revenue'
            )->addColumn(
                'completed_checkouts_count',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Completed Checkouts Count'
            )->addColumn(
                'completed_checkouts_revenue',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Completed Checkouts Revenue'
            )->addColumn(
                'conversion',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Conversion'
            )->addColumn(
                'base_to_global_rate',
                Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Base To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_osc_report_abandoned_checkouts_index_idx', ['period']),
                ['period']
            )->addIndex(
                $installer->getIdxName('aw_osc_report_abandoned_checkouts_index_idx', ['store_id']),
                ['store_id']
            )->addIndex(
                $installer->getIdxName('aw_osc_report_abandoned_checkouts_index_idx', ['customer_group_id']),
                ['customer_group_id']
            )->addForeignKey(
                $installer->getFkName('aw_osc_report_abandoned_checkouts_index_idx', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Abandoned Checkouts Index Idx Table');
        $installer->getConnection()->createTable($table);

        foreach ($this->aggregation->getAggregations() as $aggregation) {
            $tableName = 'aw_osc_report_abandoned_checkouts_index_aggregated_by_' . $aggregation;
            $table = $installer->getConnection()
                ->newTable($installer->getTable($tableName))
                ->addColumn(
                    'index_id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Index Id'
                )->addColumn(
                    'period_from',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => false, 'primary' => true],
                    'Period From Date'
                )->addColumn(
                    'period_to',
                    Table::TYPE_DATE,
                    null,
                    ['nullable' => false, 'primary' => true],
                    'Period To Date'
                )->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Store Id'
                )->addColumn(
                    'customer_group_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer Group Id'
                )->addColumn(
                    'abandoned_checkouts_count',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Abandoned Checkouts Count'
                )->addColumn(
                    'abandoned_checkouts_revenue',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Abandoned Checkouts Revenue'
                )->addColumn(
                    'completed_checkouts_count',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Completed Checkouts Count'
                )->addColumn(
                    'completed_checkouts_revenue',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Completed Checkouts Revenue'
                )->addColumn(
                    'conversion',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Conversion'
                )->addColumn(
                    'base_to_global_rate',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [],
                    'Base To Global Rate'
                )->addColumn(
                    'base_to_global_rate',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [],
                    'Base To Global Rate'
                )->addIndex(
                    $installer->getIdxName($tableName, ['period_from']),
                    ['period_from']
                )->addIndex(
                    $installer->getIdxName($tableName, ['period_to']),
                    ['period_to']
                )->addIndex(
                    $installer->getIdxName($tableName, ['store_id']),
                    ['store_id']
                )->addIndex(
                    $installer->getIdxName($tableName, ['customer_group_id']),
                    ['customer_group_id']
                )->addForeignKey(
                    $installer->getFkName($tableName, 'store_id', 'store', 'store_id'),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    Table::ACTION_CASCADE
                )->setComment('Abandoned Checkouts Index Aggregated By ' . ucfirst($aggregation) . ' Table');
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table 'aw_osc_checkout_data_completeness'
         */
        $table = $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)
            ->newTable($installer->getTable('aw_osc_checkout_data_completeness', self::CHECKOUT_CONNECTION_NAME))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote Id'
            )->addColumn(
                'field_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Field Name'
            )->addColumn(
                'is_completed',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Is Completed'
            )->addColumn(
                'scope',
                Table::TYPE_TEXT,
                255,
                [],
                'Scope'
            )->addIndex(
                $installer->getIdxName('aw_osc_checkout_data_completeness', ['quote_id']),
                ['quote_id']
            )->addForeignKey(
                $installer->getFkName('aw_osc_checkout_data_completeness', 'quote_id', 'quote', 'entity_id'),
                'quote_id',
                $installer->getTable('quote', self::CHECKOUT_CONNECTION_NAME),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Checkouts Data Completeness Table');
        $installer->getConnection(self::CHECKOUT_CONNECTION_NAME)->createTable($table);
    }
}
