<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\CustomForm\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            if ($installer->tableExists('mageplaza_custom_form_form')) {
                $connection->addColumn(
                    $installer->getTable('mageplaza_custom_form_form'),
                    'identifier',
                    [
                        'type'    => Table::TYPE_TEXT,
                        null,
                        'comment' => 'Identifier',
                        'after'   => 'name'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            if ($installer->tableExists('mageplaza_custom_form_form')) {
                $data = [
                    'views'                    => [
                        'type'    => Table::TYPE_INTEGER,
                        'default' => 0,
                        'comment' => 'Views'
                    ],
                    'admin_nof_cc_to_email'    => [
                        'type'    => Table::TYPE_TEXT,
                        'comment' => 'Admin Notification CC To Email'
                    ],
                    'admin_nof_bcc_to_email'   => [
                        'type'    => Table::TYPE_TEXT,
                        'comment' => 'Admin Notification BCC To Email'
                    ],
                    'admin_nof_attached_files' => [
                        'type'    => Table::TYPE_INTEGER,
                        'comment' => 'Admin Notification Attached Files'
                    ],
                    'auto_res_attached_files'  => [
                        'type'    => Table::TYPE_INTEGER,
                        'comment' => 'Auto-responder Attached Files'
                    ],
                ];

                foreach ($data as $column => $type) {
                    $connection->addColumn(
                        $installer->getTable('mageplaza_custom_form_form'),
                        $column,
                        $type
                    );
                }
            }
        }

        $installer->endSetup();
    }
}
