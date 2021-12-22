<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema For Pricelevel table
 */
class InstallSchema implements InstallSchemaInterface
{

    const PRICELEVEL_ID = 'pricelevel_id';
    const NULLABLE = 'nullable';

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup *
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;

        $installer->startSetup();

        $erpPriceLevel = $installer->getConnection()
            ->newTable($installer->getTable('i95dev_pricelevels'))
            ->addColumn(
                self::PRICELEVEL_ID,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, self::NULLABLE => false, 'primary' => true],
                'PRICE LEVEL ID'
            )
            ->addColumn(
                'pricelevel_code',
                Table::TYPE_TEXT,
                null,
                [self::NULLABLE => false],
                'PRICE LEVEL'
            )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                null,
                [self::NULLABLE => false],
                'PRICE LEVEL DESCRIPTION'
            )
            ->addIndex(
                $installer->getIdxName(
                    'i95dev_pricelevels',
                    [self::PRICELEVEL_ID],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [self::PRICELEVEL_ID],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('GP Price Levels');

        $installer->getConnection()->createTable($erpPriceLevel);

        $erpPriceList = $installer->getConnection()
            ->newTable($installer->getTable('i95dev_erp_pricelevel_price'))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                ['identity' => true, self::NULLABLE => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'sku',
                Table::TYPE_TEXT,
                null,
                [self::NULLABLE => false],
                'SKU'
            )
            ->addColumn(
                'qty',
                Table::TYPE_DECIMAL,
                '12,4',
                [self::NULLABLE => false, 'default' => '1.0000'],
                'QTY'
            )
            ->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                '12,4',
                [self::NULLABLE => false, 'default' => '0.0000'],
                'PRICE'
            )
            ->addColumn(
                'pricelevel',
                Table::TYPE_TEXT,
                null,
                [self::NULLABLE => false],
                'PRICELEVEL'
            )
            ->setComment('GP Item Price Levels');

        $installer->getConnection()->createTable($erpPriceList);
        $installer->endSetup();
    }
}
