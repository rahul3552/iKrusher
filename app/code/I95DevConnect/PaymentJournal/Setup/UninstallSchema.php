<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Setup;

/**
 * Interface for handling data removal during module uninstall
 *
 * @api
 */
class UninstallSchema implements \Magento\Framework\Setup\UninstallInterface
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConn;

    /**
     * UninstallSchema constructor.
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
     * @author Hrusikesh Manna
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();
        $this->dropCustomTable($setup);
        $connection = $this->resourceConn->getConnection();
        $connection->delete(
            $setup->getTable('setup_module'),
            ['module = ?' => 'I95DevConnect_PaymentJournal']
        );
        $setup->endSetup();
    }

    /**
     * drop connector table during uninstallation
     * @param $setup
     * @author Hrusikesh Manna
     */
    public function dropCustomTable($setup)
    {
        $path = [
            'i95dev_payment_journal'
        ];

        foreach ($path as $configPath) {
            if ($setup->getConnection()->isTableExists($setup->getTable($configPath))) {
                $setup->getConnection()->dropTable($setup->getTable($configPath));
            }
        }
    }
}
