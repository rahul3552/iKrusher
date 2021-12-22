<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\PriceLevel\Setup;

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

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $tableName = $setup->getTable('i95dev_erp_pricelevel_price');
            if ($setup->getConnection()->isTableExists($tableName)) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $tableName,
                    'from_date',
                    [
                        'type' => Table::TYPE_DATE,
                        'nullable' => true,
                        'comment' => 'From Date'
                    ]
                );
                $connection->addColumn(
                    $tableName,
                    'to_date',
                    [
                        'type' => Table::TYPE_DATE,
                        'nullable' => true,
                        'comment' => 'To Date'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
