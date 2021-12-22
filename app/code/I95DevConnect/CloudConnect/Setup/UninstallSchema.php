<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Setup;

/**
 * Interface for handling data removal during module uninstall
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{

    public $customerSetupFactory;

    /**
     * Constructor for DI
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
     * @param \Magento\Framework\App\ResourceConnection $resourceConn
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
        \Magento\Framework\App\ResourceConnection $resourceConn,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {

        $this->resourceConfig = $resourceConfig;
        $this->resourceConn = $resourceConn;
        $this->storeManager = $storeManager;
    }

    /**
     * Invoked when remove-data flag is set during module uninstall.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();
        $this->deleteConfigurations();

        $connection = $this->resourceConn->getConnection();
        /**
         * @updatedBy vinayakrao.shetkar, replaced with Magento Standard Query
         */
        $connection->delete(
            $setup->getTable('setup_module'),
            ['module = ?' => 'I95DevConnect_CloudConnect']
        );

        $setup->endSetup();
    }

    public function deleteConfigurations()
    {
        $path = [
            'i95dev_adapter_configurations/enabled_disabled/token',
            'i95dev_adapter_configurations/enabled_disabled/enabled',
            'i95dev_adapter_configurations/enabled_disabled/target_url',
            'i95dev_adapter_configurations/enabled_disabled/client_id',
            'i95dev_adapter_configurations/enabled_disabled/subscription_key',
            'i95dev_adapter_configurations/enabled_disabled/endpoint_code',
            'i95dev_adapter_configurations/enabled_disabled/instance_type',
            'i95dev_adapter_configurations/enabled_disabled/logs_enabled',
            'i95dev_adapter_configurations/enabled_disabled/crmerp'
        ];

        foreach ($path as $configPath) {
            $this->resourceConfig->deleteConfig(
                $configPath
            );
        }
    }
}
