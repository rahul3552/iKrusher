<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2021 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\ShippingMapping\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class for handling table update during module install
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.6", "<")) {
            $tableName = $setup->getTable('i95dev_shipping_mapping_list');
            if ($setup->getConnection()->isTableExists($tableName)) {
                $connection = $setup->getConnection();
                $connection->modifyColumn(
                    $tableName,
                    'erp_code',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'ERP Method Code'
                    ]
                );
                $connection->modifyColumn(
                    $tableName,
                    'magento_code',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'Magento Method Code'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
