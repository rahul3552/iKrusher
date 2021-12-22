<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_CancelOrder
 */
namespace I95DevConnect\CancelOrder\Setup;

/**
 * class for removing cancel order module entry from db
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{

    /**
     * UninstallSchema constructor.
     *
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
     * @param \Magento\Framework\App\ResourceConnection                    $resourceConn
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
     * @param  \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param  \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();

        $this->resourceConfig->deleteConfig(
            'i95devconnect_CancelOrder/cancelorder_enabled_settings/enable_cancelorder'
        );

        $connection = $this->resourceConn->getConnection();
        $connection->delete(
            $setup->getTable('setup_module'),
            [
                'module = ?' => 'I95DevConnect_CancelOrder'
            ]
        );
        $setup->endSetup();
    }
}
