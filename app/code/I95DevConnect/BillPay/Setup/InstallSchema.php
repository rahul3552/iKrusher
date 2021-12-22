<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    const PRIMARY_ID = 'primary_id';
    const IDENTITY = 'identity';
    const NULLABLE = 'nullable';
    const PRIMARY = 'primary';
    const PAYMENT_ID = 'payment_id';
    const PAYMENTID = 'PAYMENT ID';
    const PRIMARYID = 'PRIMARY ID';
    /**
     * Installs DB schema for a module
     *
     * @param  SchemaSetupInterface $setup *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;

        $installer->startSetup();
        $arBook = $this->addArBookColumns($installer);
        $installer->getConnection()->createTable($arBook);
        $arPayment = $this->addArPaymentColumns($installer);
        $installer->getConnection()->createTable($arPayment);

        $arPaymentDetails = $installer->getConnection()
            ->newTable($installer->getTable('i95dev_ar_payment_details'))
            ->addColumn(
                self::PRIMARY_ID,
                Table::TYPE_BIGINT,
                null,
                [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true],
                self::PRIMARYID
            )
            ->addColumn(
                self::PAYMENT_ID,
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                self::PAYMENTID
            )
            ->addColumn(
                'target_invoice_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'TARGET INVOICE ID'
            )
            ->addColumn(
                'amount',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'AMOUNT'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'STATUS'
            )
            ->addColumn(
                'ar_id',
                Table::TYPE_INTEGER,
                null,
                [self::NULLABLE => false],
                'AR ID'
            )
            ->setComment('i95dev ar payment details');

        $installer->getConnection()->createTable($arPaymentDetails);
        $installer->endSetup();
    }
    public function addArPaymentColumns($installer)
    {
        return $installer->getConnection()
            ->newTable($installer->getTable('i95dev_ar_payment'))
            ->addColumn(
                self::PRIMARY_ID,
                Table::TYPE_BIGINT,
                null,
                [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true, 'auto_increment' => 100000],
                self::PRIMARYID
            )
            ->addColumn(
                'payment_type',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'PAYMENT TYPE'
            )
            ->addColumn(
                'payment_date',
                Table::TYPE_DATETIME,
                null,
                [self::NULLABLE => false],
                'PAYMENT DATE'
            )
            ->addColumn(
                'payment_trans_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'PAYMENT TRANS ID'
            )
            ->addColumn(
                self::PAYMENT_ID,
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                self::PAYMENTID
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'STATUS'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'CUSTOMER ID'
            )
            ->addColumn(
                'total_amt',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'TOTAL AMT'
            )
            ->addColumn(
                'target_sync_status',
                Table::TYPE_SMALLINT,
                null,
                [self::NULLABLE => false,'default' => '1'],
                'TARGET SYNC STATUS'
            )
            ->addColumn(
                'paypalcim_profile_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'PAYPALCIM PROFILE ID'
            )
            ->addColumn(
                'cash_receipt_number',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'CASH RECEIPT NUMBER'
            )
            ->addColumn(
                'payment_comment',
                Table::TYPE_TEXT,
                null,
                [self::NULLABLE => false],
                'PAYMENT COMMENT'
            )
            ->setComment('i95dev arpayment');
    }

    public function addArBookColumns($installer)
    {
        return $installer->getConnection()
            ->newTable($installer->getTable('i95dev_ar_book'))
            ->addColumn(
                self::PRIMARY_ID,
                Table::TYPE_BIGINT,
                null,
                [self::IDENTITY => true, self::NULLABLE => false, self::PRIMARY => true],
                self::PRIMARYID
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'CUSTOMER ID'
            )
            ->addColumn(
                'target_customer_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'TARGET CUSTOMER ID'
            )
            ->addColumn(
                'target_invoice_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'TARGET INVOICE ID'
            )
            ->addColumn(
                'target_order_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'TARGET ORDER ID'
            )
            ->addColumn(
                'magento_order_id',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'MAGENTO ORDER ID'
            )
            ->addColumn(
                'invoice_amount',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'INVOICE AMOUNT'
            )
            ->addColumn(
                'outstanding_amount',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'OUTSTANDING AMOUNT'
            )
            ->addColumn(
                'interest_amount',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => true],
                'INTEREST AMOUNT'
            )
            ->addColumn(
                'discount_amount',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => true],
                'DISCOUNT AMOUNT'
            )
            ->addColumn(
                'outstanding_amount',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'OUTSTANDING AMOUNT'
            )
            ->addColumn(
                'customer_po_number',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'CUSTOMER PO NUMBER'
            )
            ->addColumn(
                'order_status',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                'ORDER STATUS'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                50,
                [self::NULLABLE => false],
                'TYPE'
            )
            ->addColumn(
                'modified_date',
                Table::TYPE_DATETIME,
                null,
                [self::NULLABLE => false],
                'MODIFIED DATE'
            )
            ->addColumn(
                'due_date',
                Table::TYPE_TIMESTAMP,
                null,
                [self::NULLABLE => false],
                'DUE DATE'
            )
            ->addColumn(
                'modified_by',
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => true],
                'MODIFIED BY'
            )
            ->addColumn(
                self::PAYMENT_ID,
                Table::TYPE_TEXT,
                200,
                [self::NULLABLE => false],
                self::PAYMENTID
            )
            ->setComment('I95dev Ar Book');
    }
}
