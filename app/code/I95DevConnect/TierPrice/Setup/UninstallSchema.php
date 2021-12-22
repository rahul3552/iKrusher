<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_TierPrice
 */

namespace I95DevConnect\TierPrice\Setup;

/**
 * Interface for handling data removal during module uninstall
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{

    public $customerSetupFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConn;

    /**
     * UninstallSchema constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resourceConn
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConn
    ) {
        $this->resourceConn = $resourceConn;
    }

    /**
     * Invoked when remove-data flag is set during module uninstall.
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @author i95Dev Team
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
            ['module = ?' => 'I95DevConnect_TierPrice']
        );

        $setup->endSetup();
    }
}
