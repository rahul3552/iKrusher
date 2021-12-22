<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Setup;

/**
 * Class for delete configuration from DB during un installation
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{

    public $resourceConfig;
    public $resourceConn;

    /**
     * Constructer for DI
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
     * @param \Magento\Framework\App\ResourceConnection $resourceConn
     */
    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
        \Magento\Framework\App\ResourceConnection $resourceConn
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->resourceConn = $resourceConn;
    }

    /**
     * Invoked when remove-data flag is set during module uninstall.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @author Hrusikesh Manna
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if ($setup->getConnection()->isTableExists($setup->getTable('i95dev_pricelevels'))) {
            $setup->getConnection()->dropTable($setup->getTable('i95dev_pricelevels'));
        }
        if ($setup->getConnection()->isTableExists($setup->getTable('i95dev_erp_pricelevel_price'))) {
            $setup->getConnection()->dropTable($setup->getTable('i95dev_erp_pricelevel_price'));
        }
        $connection = $this->resourceConn->getConnection();
        $this->deleteMQConfigurations();
        $connection->delete(
            $setup->getTable('setup_module'),
            ['module = ?' => 'I95DevConnect_PriceLevel']
        );
        $setup->endSetup();
    }

    /**
     * Delete Extension Configuratio Dusring Uninstall
     * @author Hrusikesh Manna
     */
    public function deleteMQConfigurations()
    {
        $this->resourceConfig->deleteConfig(
            'i95dev_pricelevel/active_display/enabled'
        );
    }
}
