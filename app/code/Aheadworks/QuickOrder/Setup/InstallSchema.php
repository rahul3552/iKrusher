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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList as ProductListSource;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item as ItemSource;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\QuickOrder\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createProductListTable($installer);
        $this->createProductListItemTable($installer);

        $installer->endSetup();
    }

    /**
     * Add product list table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createProductListTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_qo_product_list'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ProductListSource::MAIN_TABLE_NAME))
            ->addColumn(
                ProductListInterface::LIST_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'List ID'
            )->addColumn(
                ProductListInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Customer ID'
            )->addColumn(
                ProductListInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->setComment('Quick Order Product List Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add product list item table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createProductListItemTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_qo_product_list_item'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ItemSource::MAIN_TABLE_NAME))
            ->addColumn(
                ProductListItemInterface::ITEM_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Item ID'
            )->addColumn(
                ProductListItemInterface::ITEM_KEY,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Item Key'
            )->addColumn(
                ProductListItemInterface::LIST_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'List ID'
            )->addColumn(
                ProductListItemInterface::PRODUCT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                ProductListItemInterface::PRODUCT_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                ProductListItemInterface::PRODUCT_TYPE,
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Product Type'
            )->addColumn(
                ProductListItemInterface::PRODUCT_SKU,
                Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'Product Sku'
            )->addColumn(
                ProductListItemInterface::PRODUCT_QTY,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Product Quantity'
            )->addColumn(
                ProductListItemInterface::PRODUCT_OPTION,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Product Option'
            )->addIndex(
                $installer->getIdxName(ItemSource::MAIN_TABLE_NAME, [ProductListItemInterface::LIST_ID]),
                [ProductListItemInterface::LIST_ID]
            )->addForeignKey(
                $installer->getFkName(
                    ItemSource::MAIN_TABLE_NAME,
                    ProductListItemInterface::LIST_ID,
                    ProductListSource::MAIN_TABLE_NAME,
                    ProductListInterface::LIST_ID
                ),
                ProductListItemInterface::LIST_ID,
                $installer->getTable(ProductListSource::MAIN_TABLE_NAME),
                ProductListInterface::LIST_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    ItemSource::MAIN_TABLE_NAME,
                    ProductListItemInterface::PRODUCT_ID,
                    'catalog_product_entity',
                    'entity_id'
                ),
                ProductListItemInterface::PRODUCT_ID,
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Quick Order Product List Item Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }
}
