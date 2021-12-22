<?php

/**
 * @author Arushi Bansal
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\ShippingMapping\Setup;

/**
 * class for removing shipping mapping module entry from db
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{

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
     * Invoked when remove-data flag is set during module uninstall.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();

        $this->deleteMQConfigurations();

        $setup->getConnection()->dropTable($setup->getTable('i95dev_shipping_mapping_list'));

        $connection = $this->resourceConn->getConnection();
        $connection->delete(
            $setup->getTable('setup_module'),
            ['module = ?' => 'I95DevConnect_ShippingMapping']
        );

        $setup->endSetup();
    }

    /**
     * delete messagequeue configuration entries during uninstallation
     */
    protected function deleteMQConfigurations()
    {
        $path = [
            'i95dev_adapter_configurations/i95dev_shipping_mapping/enabled'
        ];

        foreach ($path as $configPath) {
            $this->resourceConfig->deleteConfig(
                $configPath
            );
        }
    }
}
