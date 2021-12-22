<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */

namespace I95DevConnect\I95DevServer\Setup;

/**
 * Class for handling i95devServer module entry removal from DB
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $resourceConn;

    /**
     * Constructor for DI
     * @param \Magento\Framework\App\ResourceConnection $resourceConn
     * @createdBy Arushi Bansal
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConn
    ) {
        $this->resourceConn = $resourceConn;
    }

    /**
     * remove i95devServer module from setup_module
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @return void
     * @createdBy Arushi Bansal
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();
        $connection = $this->resourceConn->getConnection();
        /**
         * @updatedBy vinayakrao.shetkar, replaced with Magento Standard Query
         */
        $connection->delete(
            $setup->getTable('setup_module'),
            ['module = ?' => 'I95DevConnect_I95DevServer']
        );

        $setup->endSetup();
    }
}
