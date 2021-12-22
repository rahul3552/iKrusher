<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_CancelOrder
 */

namespace I95DevConnect\CancelOrder\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class for install data during module instalation
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var Magento\Framework\App\State $appState
     */
    private $appState;
    
    protected $logger;
    
    /**
     * install data constructor
     *
     * @param \Magento\Framework\App\State $appState
     * @param \Psr\Log\LoggerInterface     $logger
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Psr\Log\LoggerInterface $logger
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
                [
                'entity_name' => 'Cancel Order',
                'entity_code' => 'cancelorder',
                'sort_order' => 12,
                'support_for_inbound' => true,
                'support_for_outbound' => false
                ]
            ]
        );
        $setup->endSetup();
    }
}
