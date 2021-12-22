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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item;

/**
 * Class Uninstall
 *
 * @package Aheadworks\QuickOrder\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @inheritdoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->uninstallTables($installer)
            ->uninstallConfigData($installer);

        $installer->endSetup();
    }

    /**
     * Uninstall all module tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallTables(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->dropTable($installer->getTable(Item::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(ProductList::MAIN_TABLE_NAME));
        return $this;
    }

    /**
     * Uninstall module data from config
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallConfigData(SchemaSetupInterface $installer)
    {
        $configTable = $installer->getTable('core_config_data');
        $installer->getConnection()->delete($configTable, "`path` LIKE 'aw_quick_order%'");
        return $this;
    }
}
