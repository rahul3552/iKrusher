<?php
namespace Vueai\ProductRecommendations\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    const TABLE_NAME = 'productrecommendations_signup';
    const PRIMARY_KEY = 'id';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'productrecommendations_singup'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::TABLE_NAME)
        )
        ->addColumn(
            self::PRIMARY_KEY,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'productrecommendations_signup'
        )
        ->addColumn(
            'source',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'source'
        )
        ->addColumn(
            'domain',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'domain'
        )
        ->addColumn(
            'event',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'event'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'email'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'name'
        )
        ->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'company'
        )
        ->addColumn(
            'phone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'phone'
        )
        ->addColumn(
            'region',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'region'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'status'
        )
        ->addColumn(
            'api_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'api_url'
        )
        ->addColumn(
            'embedded_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'embedded_code'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'store_id'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )

        ->setComment(
            'Vueai ProductRecommendations productrecommendations_signup'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
