<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    const ENTITY_NAME = 'entity_name';

    const ENTITY_CODE = 'entity_code';

    const SORT_ORDER = 'sort_order';

    const SUPPORT_FOR_INBOUND = 'support_for_inbound';

    const SUPPORT_FOR_OUTBOUND = 'support_for_outbound';
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0.2', '<')) {
            $setup->startSetup();

            // Get tutorial_simplenews table
            $tableName = $setup->getTable('i95dev_entity');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName)) {
                $setup->getConnection()->insertOnDuplicate(
                    $tableName,
                    [
                            [self::ENTITY_NAME => 'Account Receivables',
                                self::ENTITY_CODE => 'accountreceivables',
                                self::SORT_ORDER => 22,
                                self::SUPPORT_FOR_INBOUND => true,
                                self::SUPPORT_FOR_OUTBOUND => true],
                            [self::ENTITY_NAME => 'Cash Receipts',
                                self::ENTITY_CODE => 'cashreceipts',
                                self::SORT_ORDER => 22.1,
                                self::SUPPORT_FOR_INBOUND => true,
                                self::SUPPORT_FOR_OUTBOUND => true]
                        ]
                );
            }

            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '2.0.0.5', '<')) {
            $setup->startSetup();
            $tableName = $setup->getTable('i95dev_entity');
            if ($setup->getConnection()->isTableExists($tableName)) {
                $setup->getConnection()->insertOnDuplicate(
                    $tableName,
                    [
                        [self::ENTITY_NAME => 'Penalty', self::ENTITY_CODE => 'penalty', self::SORT_ORDER => 22.2,
                            self::SUPPORT_FOR_INBOUND => true, self::SUPPORT_FOR_OUTBOUND => true]
                    ]
                );
            }

            $setup->endSetup();
        }
    }
}
