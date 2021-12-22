<?php

/**
 * Copyright ? Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace I95DevConnect\ConfigurableProducts\Setup;

/**
 * Interface for handling data removal during module uninstall
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{

    public $resourceConfig;
    public $resourceConn;
    public $storeManager;

    /**
     * UninstallSchema constructor.
     *
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
     * @param \Magento\Framework\App\ResourceConnection                    $resourceConn
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
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
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();

        $connection = $this->resourceConn->getConnection();
        $connection->delete(
            $setup->getTable('setup_module'),
            ['module = ?' => 'I95DevConnect_ConfigurableProducts']
        );

        $this->resourceConfig->deleteConfig(
            'configurableproducts/i95dev_enabled_settings/is_enabled'
        );

        $setup->endSetup();
    }
}
