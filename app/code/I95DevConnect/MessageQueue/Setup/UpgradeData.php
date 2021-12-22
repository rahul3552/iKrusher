<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Setup;

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
    const ENTITY_NAME ='entity_name';
    const ENTITY_CODE ='entity_code';
    const SORT_ORDER ='sort_order';
    const SUPPORT_FOR_INBOUND ='support_for_inbound';
    const SUPPORT_FOR_OUTBOUND ='support_for_outbound';

    /**
     * {}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            $i95DevEntitiesArr = [
                ['Customer Group', 'CustomerGroup', 4, true, true],
                ['Customer', 'Customer', 6, true, true],
                ['Address', 'address', 6.1, true, true],
                ['Product', 'product', 1, true, true],
                ['Inventory', 'inventory', 2, true, false],
                ['Order', 'order', 7, true, true],
                ['Invoice', 'invoice', 9, true, false],
                ['Shipment', 'shipment', 8, true, false],
            ];

            $columnData = [];
            foreach ($i95DevEntitiesArr as $data) {
                $columnData[] = [
                    self::ENTITY_NAME => $data[0],
                    self::ENTITY_CODE => $data[1],
                    self::SORT_ORDER=> $data[2],
                    self::SUPPORT_FOR_INBOUND => $data[3],
                    self::SUPPORT_FOR_OUTBOUND => $data[4]
                ];
            }

            $setup->getConnection()->insertOnDuplicate(
                $setup->getTable('i95dev_entity'),
                $columnData
            );
        }

        $setup->endSetup();
    }
}
