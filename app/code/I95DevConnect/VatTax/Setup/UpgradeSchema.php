<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_Vattax
 * @createdBy Arushi Bansal
 */

namespace I95DevConnect\VatTax\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const NULLABLE = 'nullable';
    const DEFAULT = 'default';
    const LENGTH = 'length';
    const TBGC = 'tax_busposting_group_code';
    const TPPGC = 'tax_productposting_group_code';
    const CREATED_DATE = 'created_date';
    const UPDATED_DATE = 'updated_date';

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $i95DevtaxBusPosting = $setup->getTable('i95dev_tax_busposting_group');
            if ($setup->getConnection()->isTableExists($i95DevtaxBusPosting)) {
                $connection = $setup->getConnection();
                $connection->changeColumn(
                    $i95DevtaxBusPosting,
                    'code',
                    'code',
                    ['type' => Table::TYPE_TEXT, self::NULLABLE => false, self::DEFAULT => 0, self::LENGTH => 15],
                    'Code'
                );
                $connection->addIndex(
                    $i95DevtaxBusPosting,
                    $setup->getIdxName($i95DevtaxBusPosting, 'code'),
                    ['code'],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
            }

            $i95DevtaxProductPosting = $setup->getTable('i95dev_tax_productposting_group');
            if ($setup->getConnection()->isTableExists($i95DevtaxProductPosting)) {
                $connection = $setup->getConnection();
                $connection->changeColumn(
                    $i95DevtaxProductPosting,
                    'code',
                    'code',
                    ['type' => Table::TYPE_TEXT, self::NULLABLE => false, self::DEFAULT => 0, self::LENGTH => 15],
                    'Code'
                );
                $connection->addIndex(
                    $i95DevtaxProductPosting,
                    $setup->getIdxName($i95DevtaxProductPosting, 'code'),
                    ['code'],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
            }

            $i95DevtaxPosting = $setup->getTable('i95dev_tax_postingsetup');
            if ($setup->getConnection()->isTableExists($i95DevtaxPosting)) {
                $connection = $setup->getConnection();
                $connection->changeColumn(
                    $i95DevtaxPosting,
                    self::TBGC,
                    self::TBGC,
                    ['type' => Table::TYPE_TEXT, self::NULLABLE => false, self::DEFAULT => 0, self::LENGTH => 15],
                    'Tax Bus Posting Group Code'
                );
                $connection->changeColumn(
                    $i95DevtaxPosting,
                    self::TPPGC,
                    self::TPPGC,
                    ['type' => Table::TYPE_TEXT, self::NULLABLE => false, self::DEFAULT => 0, self::LENGTH => 15],
                    'Tax Product Posting Group Code'
                );
                $connection->addIndex(
                    $i95DevtaxPosting,
                    $setup->getIdxName($i95DevtaxPosting, 'posting_code_idx'),
                    [self::TBGC, self::TPPGC],
                    AdapterInterface::INDEX_TYPE_INDEX
                );
            }
        }

        if (version_compare($context->getVersion(), '2.0.3', '<')) {

            $i95DevtaxBusPosting = $setup->getTable('i95dev_tax_busposting_group');
            if ($setup->getConnection()->isTableExists($i95DevtaxBusPosting)) {
                $connection = $setup->getConnection();
                $connection->dropColumn($i95DevtaxBusPosting, self::CREATED_DATE);
                $connection->dropColumn($i95DevtaxBusPosting, self::UPDATED_DATE);
            }

            $i95DevtaxPosting = $setup->getTable('i95dev_tax_postingsetup');
            if ($setup->getConnection()->isTableExists($i95DevtaxPosting)) {
                $connection = $setup->getConnection();
                $connection->dropColumn($i95DevtaxPosting, self::CREATED_DATE);
                $connection->dropColumn($i95DevtaxPosting, self::UPDATED_DATE);
            }

            $i95DevtaxProductPosting = $setup->getTable('i95dev_tax_productposting_group');
            if ($setup->getConnection()->isTableExists($i95DevtaxProductPosting)) {
                $connection = $setup->getConnection();
                $connection->dropColumn($i95DevtaxProductPosting, self::CREATED_DATE);
                $connection->dropColumn($i95DevtaxProductPosting, self::UPDATED_DATE);
            }

        }

        $setup->endSetup();
    }
}
