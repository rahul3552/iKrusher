<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax 
 */

namespace I95DevConnect\VatTax\Setup;

/**
 * Interface for handling data removal during module uninstall
 *
 * @author Arushi Bansal
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface {

    public $resourceConfig;
    public $resourceConn;

    /**
     * UninstallSchema constructor.
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
     * Invoked when remove-data flag is set during module uninstall
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    // @codingStandardsIgnoreFile
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();
        $this->dropMQTable($setup);
        $this->deleteMQConfigurations();

        $connection = $this->resourceConn->getConnection();
        $connection->delete(
            $setup->getTable('setup_module'), ['module = ?' => 'I95DevConnect_VatTax']
        );

        $setup->endSetup();
    }

    /**
     * delete messagequeue configuration entries during uninstallation
     */
    function deleteMQConfigurations() {
        $path = [
            'i95devconnect_vattax/vattax_enabled_settings/enable_vattax',
        ];

        foreach ($path as $configPath) {
            $this->resourceConfig->deleteConfig(
                $configPath
            );
        }
    }

    /**
     * drop connector table during uninstallation
     * @param $setup
     */
    function dropMQTable($setup) {
        $path = [
            'i95dev_tax_busposting_group',
            'i95dev_tax_productposting_group',
            'i95dev_tax_postingsetup'
        ];

        foreach ($path as $configPath) {
            if ($setup->getConnection()->isTableExists($setup->getTable($configPath))) {
                $setup->getConnection()->dropTable($setup->getTable($configPath));
            }
        }
    }

}
