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
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\CustomForm\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('mageplaza_custom_form_form')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_custom_form_form'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ], 'Form ID')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Form Name')
                ->addColumn('status', Table::TYPE_INTEGER, 1, ['nullable' => false], 'Status')
                ->addColumn('store_ids', Table::TYPE_TEXT, 255, [], 'Stores')
                ->addColumn('customer_group_ids', Table::TYPE_TEXT, 255, [], 'Customer Groups')
                ->addColumn('valid_from_date', Table::TYPE_TIMESTAMP, null, [], 'Valid From Date')
                ->addColumn('valid_to_date', Table::TYPE_TIMESTAMP, null, [], 'Valid To Date')
                ->addColumn('form_style', Table::TYPE_TEXT, 255, [], 'Form Style')
                ->addColumn('fb_button_text', Table::TYPE_TEXT, 255, [], 'Form Style')
                ->addColumn('popup_type', Table::TYPE_TEXT, 10, [], 'Popup Type')
                ->addColumn('custom_css', Table::TYPE_TEXT, '2M', [], 'Custom CSS')
                ->addColumn('action_after_submit', Table::TYPE_TEXT, 10, [], 'Action After Submit Form')
                ->addColumn('page_url', Table::TYPE_TEXT, 255, [], 'Action After Submit URL')
                ->addColumn('cms_page', Table::TYPE_TEXT, 255, [], 'Action After Submit Cms Page')
                ->addColumn('custom_form', Table::TYPE_TEXT, '2M', [], 'Custom Form')
                ->addColumn('admin_nof_enabled', Table::TYPE_INTEGER, 1, [], 'Admin Notification Enable')
                ->addColumn('admin_nof_send_to', Table::TYPE_TEXT, 512, [], 'Admin Notification Send To')
                ->addColumn('admin_nof_send_time', Table::TYPE_TEXT, 20, [], 'Admin Notification Send Time')
                ->addColumn('admin_nof_sender', Table::TYPE_TEXT, 225, [], 'Admin Notification Sender')
                ->addColumn('admin_nof_template', Table::TYPE_TEXT, 225, [], 'Admin Notification Email Template')
                ->addColumn('auto_res_enabled', Table::TYPE_INTEGER, 1, [], 'Auto-responder Enable')
                ->addColumn('auto_res_email_address', Table::TYPE_TEXT, 255, [], 'Auto-responder Email address field')
                ->addColumn('auto_res_sender', Table::TYPE_TEXT, 225, [], 'Auto-responder Sender')
                ->addColumn('auto_res_template', Table::TYPE_TEXT, 225, [], 'Auto-responder Email Template')
                ->addColumn('email_planing', Table::TYPE_TEXT, '1M', [], 'Auto-responder Email planing')
                ->addColumn('responses_summary', Table::TYPE_TEXT, '2M', [], 'Response Summary')
                ->addColumn(
                    'last_responses_update',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Last Time Updated Response Summary'
                )
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                ->addIndex($setup->getIdxName('mageplaza_custom_form_form', ['id']), ['id'])
                ->setComment('Custom Form Table');

            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('mageplaza_custom_form_responses')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_custom_form_responses'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ], 'Form ID')
                ->addColumn('form_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Form Id')
                ->addForeignKey(
                    $setup->getFkName(
                        'mageplaza_custom_form_responses',
                        'form_id',
                        'mageplaza_custom_form_form',
                        'id'
                    ),
                    'form_id',
                    $setup->getTable('mageplaza_custom_form_form'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addColumn('customer_id', Table::TYPE_INTEGER, null, [], 'Customer Id')
                ->addColumn('store_ids', Table::TYPE_INTEGER, null, [], 'Store Id')
                ->addColumn('store_name', Table::TYPE_TEXT, 255, [], 'Store Name')
                ->addColumn('ip_address', Table::TYPE_TEXT, 255, [], 'IP Address')
                ->addColumn('form_data', Table::TYPE_TEXT, '2M', [], 'Form Data')
                ->addColumn(
                    'is_complete',
                    Table::TYPE_INTEGER,
                    1,
                    ['default' => 0, 'nullable' => false],
                    'Is Email Planing Completed'
                )
                ->addColumn(
                    'admin_nof',
                    Table::TYPE_INTEGER,
                    1,
                    ['default' => 0, 'nullable' => false],
                    'Admin Notification'
                )
                ->addColumn('email_planing', Table::TYPE_TEXT, '1M', [], 'Email Planing')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                ->addIndex($setup->getIdxName('mageplaza_custom_form_responses', ['id']), ['id'])
                ->setComment('Custom Form Responses Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
