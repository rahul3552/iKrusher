<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_TierPrice
 */
namespace I95DevConnect\TierPrice\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Interface for handling data adding during module install
 */
class InstallData implements InstallDataInterface
{

   /**
    * Installs DB schema for a TierPrice module
    * @param ModuleDataSetupInterface $setup
    * @param ModuleContextInterface $context
    * @return void
    * @author i95Dev Team
    */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $setup->getConnection()->insertOnDuplicate(
            $setup->getTable('i95dev_entity'),
            [
                [
                    'entity_name' => 'Tier Price',
                    'entity_code' => 'tierprice',
                    'sort_order'=>11,
                    'support_for_inbound' => true,
                    'support_for_outbound' => false
                ]
            ]
        );
        $setup->endSetup();
    }
}
