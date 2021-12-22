<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Class for handling data adding during module install
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var Magento\Framework\App\State $appState
     */
    private $appState;

    /**
     *
     * @param \Magento\Framework\App\State $appState
     * @param LoggerInterface $loger
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        LoggerInterface $loger
    ) {
        $this->appState = $appState;
        $this->logger = $loger;
    }

    /**
     * Installs DB schema for payment journal
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $this->appState->setAreaCode('global');
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                'critical'
            );
        }
        $setup->startSetup();
        $setup->getConnection()->insertOnDuplicate(
            $setup->getTable('i95dev_entity'),
            [
                    [
                    'entity_name' => 'Cash Receipt',
                    'entity_code' => 'cashReceipt',
                    'sort_order'=>11,
                    'support_for_inbound' => false,
                    'support_for_outbound' => true
                    ]
                ]
        );
        $setup->endSetup();
    }
}
