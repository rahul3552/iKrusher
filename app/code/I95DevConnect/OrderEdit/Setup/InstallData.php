<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_OrderEdit
 */
namespace I95DevConnect\OrderEdit\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Psr\Log\LoggerInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var Magento\Framework\App\State $appState
     */
    private $appState;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * install data constructor
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->logger = $logger;
    }

    /**
     * Installs DB schema for a module
     *
     * @param  ModuleDataSetupInterface $setup
     * @param  ModuleContextInterface   $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $this->appState->setAreaCode('frontend');
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->logger->debug($exception->getMessage());
        }

        $setup->startSetup();
        $setup->getConnection()->insertOnDuplicate(
            $setup->getTable('i95dev_entity'),
            [
                ['entity_name' => 'Edit Order',
                    'entity_code' => 'editOrder',
                    'sort_order'=>17,
                    'support_for_inbound' => true,
                    'support_for_outbound' => false]
            ]
        );
        $setup->endSetup();
    }
}
