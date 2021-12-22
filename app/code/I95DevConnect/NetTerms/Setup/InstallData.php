<?php

/**
 * I95Dev.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.I95Dev.com/LICENSE-M1.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@I95Dev.com so we can send you a copy immediately.
 *
 * @category    I95Dev
 * @package     I95DevConnect_KitProduct
 * @Description Insert Entity Values
 * @author      I95Dev
 * @copyright   Copyright (c) 2016 I95Dev
 * @license     http://store.I95Dev.com/LICENSE-M1.txt
 */

namespace I95DevConnect\NetTerms\Setup;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
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
     * install data constructor
     * @param State $appState
     */
    public function __construct(
        State $appState,
        LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->logger = $logger;
    }

    /**
     * Installs DB schema for a module
     * Updated By @Ranjith
     *
     * @param  ModuleDataSetupInterface $setup
     * @param  ModuleContextInterface   $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /* Set the area code and catch exception if thrown */
        try {
            $this->appState->setAreaCode('global');
        } catch (LocalizedException $exception) {
            $this->logger->debug($exception->getMessage());
        }
        // @codingStandardsIgnoreStart
        $setup->startSetup();
        $setup->getConnection()->insertOnDuplicate(
            $setup->getTable('i95dev_entity'),
            [
                ['entity_name' => 'NetTerms','entity_code' => 'paymentTerm','sort_order'=>4, 'support_for_inbound' => true, 'support_for_outbound' => false]
            ]
        );
        $setup->endSetup();
        // @codingStandardsIgnoreEnd
    }
}
