<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class for install schema during module install
 */
class InstallSchema implements InstallSchemaInterface
{
    const NULLABLE = "nullable";

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        // Get i95dev_payment_journal table
        $paymentJournalTable = $installer->getTable('i95dev_payment_journal');
        // Check if the table already exists
        if (!$installer->getConnection()->isTableExists($paymentJournalTable)) {
            $paymentJournal = $installer->getConnection()
                ->newTable($installer->getTable('i95dev_payment_journal'))
                ->addColumn(
                    'id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, self::NULLABLE => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'target_invoice_id',
                    Table::TYPE_TEXT,
                    35,
                    [self::NULLABLE => true],
                    'Target Invoice Id'
                )
                ->addColumn(
                    'source_invoice_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Source Invoice Id'
                )
                ->addColumn(
                    'source_order_id',
                    Table::TYPE_INTEGER,
                    null,
                    [self::NULLABLE => false],
                    'Source Order Id'
                )
                ->addColumn(
                    'created_dt',
                    Table::TYPE_DATETIME,
                    null,
                    [self::NULLABLE => false],
                    'Created Date'
                )
                ->addColumn(
                    'updated_dt',
                    Table::TYPE_DATETIME,
                    null,
                    [self::NULLABLE => false],
                    'Updated Date'
                )
                ->addColumn(
                    'receipt_id',
                    Table::TYPE_TEXT,
                    35,
                    [self::NULLABLE => true],
                    'Receipt Id'
                )
                ->setComment('i95dev payment journal');
                
            $installer->getConnection()->createTable($paymentJournal);
        }

        $installer->endSetup();
    }
}
