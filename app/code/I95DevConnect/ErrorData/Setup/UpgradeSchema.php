<?php

declare(strict_types=1);

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ErrorData
 */

namespace I95DevConnect\ErrorData\Setup;

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

    const ERRNOTIFICATION = "i95dev_error_notification";
    const NULLABLE = "nullable";

    /**
     * {}
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $errorTableName = $setup->getTable(self::ERRNOTIFICATION);
            if (!$setup->getConnection()->isTableExists($errorTableName)) {
                $errorTable = $setup->getConnection()->newTable($errorTableName)
                ->addColumn(
                    'id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, self::NULLABLE => false, 'primary' => true],
                    'Id'
                )
                ->addColumn('msg_id', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'Message Queue Id')
                ->addColumn('notification_sent', Table::TYPE_SMALLINT, null, [], 'Is Notification Sent')
                ->addColumn('origin', Table::TYPE_TEXT, '255', [], 'Origin')
                ->addColumn('entity_code', Table::TYPE_TEXT, '255', [], 'Entity Code')
                ->addColumn('created_at', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Created Date')
                ->addColumn('updated_at', Table::TYPE_DATETIME, null, [self::NULLABLE => false], 'Updated Date')
                ->setComment('i95Dev Error Notification')
                ->addIndex(
                    $setup->getIdxName(self::ERRNOTIFICATION, ['id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                );
                $setup->getConnection()->createTable($errorTable);
            }
            
            $errorMessageTableName = $setup->getTable('i95dev_error_message');
            if (!$setup->getConnection()->isTableExists($errorMessageTableName)) {
                $errorMessageTable = $setup->getConnection()->newTable($errorMessageTableName)
                ->addColumn(
                    'id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, self::NULLABLE => false, 'primary' => true],
                    'Id'
                )
                ->addColumn('notification_id', Table::TYPE_BIGINT, null, [self::NULLABLE => false], 'Notification Id')
                ->addColumn('message', Table::TYPE_TEXT, '255', [], 'Error Message')
                ->setComment('i95Dev Error Notification Message')
                ->addIndex(
                    $setup->getIdxName('i95dev_error_message', ['id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                );
                $setup->getConnection()->createTable($errorMessageTable);
            }
        }
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $errorTable = $setup->getTable(self::ERRNOTIFICATION);
            if ($setup->getConnection()->isTableExists($errorTable)) {
                $setup->getConnection()->addColumn(
                    $errorTable,
                    'entity_code',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        self::NULLABLE => true,
                        'comment' => 'Entity Code'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
