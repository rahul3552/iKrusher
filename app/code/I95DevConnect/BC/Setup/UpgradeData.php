<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BC
 */

namespace I95DevConnect\BC\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Upgrade Data script
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        // we are not syncing customer group from magento to BC as we have independent price group component in BC
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $setup->getConnection()->insertOnDuplicate(
                $setup->getTable('i95dev_entity'),
                [
                    [
                        'entity_name' => 'Customer Group',
                        'entity_code' => 'CustomerGroup',
                        'sort_order'=>4,
                        'support_for_inbound' => true,
                        'support_for_outbound' => false
                    ],
                ]
            );
        }

        $setup->endSetup();
    }
}
