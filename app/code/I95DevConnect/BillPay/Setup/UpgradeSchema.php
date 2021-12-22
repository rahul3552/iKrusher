<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const NULLABLE = 'nullable';
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0.2', '<')) {
            $arbookTable = $setup->getTable('i95dev_ar_book');

            $columns = [
                'discount_dt' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    self::NULLABLE => true,
                    'comment' => 'DISCOUNT DT',
                    'length' => null,
                ],
            ];

            $this->addColumnsRecursive($columns, $arbookTable, $setup);
        }

        if (version_compare($context->getVersion(), '2.0.0.3', '<')) {
            $arbookTable = $setup->getTable('i95dev_ar_book');

            $setup->getConnection()->modifyColumn(
                $arbookTable,
                'due_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.0.4', '<')) {
            $arPenaltyTable = $setup->getTable('i95dev_ar_penalty');
            if (!$setup->getConnection()->isTableExists($arPenaltyTable)) {
                $arPenalty = $setup->getConnection()
                ->newTable($arPenaltyTable)
                ->addColumn(
                    'primary_id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, self::NULLABLE => false, 'primary' => true],
                    'PRIMARY ID'
                )
                ->addColumn(
                    'penalty_id',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'PENALTY ID'
                )
                ->addColumn(
                    'penalty_amount',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'PENALTY AMOUNT'
                )
                ->addColumn(
                    'additional_amount',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'ADDITIONAL AMOUNT'
                )
                ->addColumn(
                    'amount',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'TOTAL PENALTY AMOUNT'
                )
                ->addColumn(
                    'ar_id',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'ACCOUNT RECEIVABLE ID'
                )
                ->addColumn(
                    'term',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'PENALTY TERM'
                )
                ->setComment('I95dev Ar Penalty');
                $setup->getConnection()->createTable($arPenalty);
            }

            $arPenaltyDetailsTable = $setup->getTable('i95dev_ar_penalty_details');
            if (!$setup->getConnection()->isTableExists($arPenaltyDetailsTable)) {
                $arPenaltyDetails = $setup->getConnection()
                ->newTable($arPenaltyDetailsTable)
                ->addColumn(
                    'primary_id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, self::NULLABLE => false, 'primary' => true],
                    'PRIMARY ID'
                )
                ->addColumn(
                    'penalty_id',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'PENALTY ID'
                )
                ->addColumn(
                    'reference_type',
                    Table::TYPE_TEXT,
                    50,
                    [self::NULLABLE => false],
                    'PENALTY REFERENCE TYPE'
                )
                ->addColumn(
                    'reference_id',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'PENALTY REFERENCE ID'
                )
                ->addColumn(
                    'ar_penalty_id',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'AR PENALTY ID'
                )
                ->addColumn(
                    'reference_amount',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'PENALTY REFERENCE AMOUNT'
                )
                ->addColumn(
                    'amount',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'PENALTY AMOUNT'
                )
                ->addColumn(
                    'comments',
                    Table::TYPE_TEXT,
                    200,
                    [self::NULLABLE => false],
                    'Comments'
                )
                ->setComment('I95dev Ar Penalty Details');
                $setup->getConnection()->createTable($arPenaltyDetails);
            }
        }

        $setup->endSetup();
    }

    public function addColumnsRecursive($columns, $table, $setup)
    {
        $connection = $setup->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($table, $name, $definition);
        }
    }
}
