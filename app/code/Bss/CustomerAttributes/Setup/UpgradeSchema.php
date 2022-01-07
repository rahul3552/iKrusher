<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomerAttributes\Setup;

use Bss\CustomerAttributes\Helper\Customer\Grid\NotDisplay as CustomerGridNotDisplay;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class InstallSchema
 *
 * @package Bss\CustomerAttributes\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CustomerGridNotDisplay
     */
    protected $customerGridNotDisplay;

    /**
     * UpgradeSchema constructor.
     *
     * @param CustomerGridNotDisplay $customerGridNotDisplay
     */
    public function __construct(
        CustomerGridNotDisplay $customerGridNotDisplay
    ) {
        $this->customerGridNotDisplay = $customerGridNotDisplay;
    }

    /**
     * Add column customer_attributes_address into table sales_order_address
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.2.4', '<')) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $installer->getTable('sales_order_address'),
                'customer_address_attribute',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Custom address attribute'
                ]
            );
            $connection->addColumn(
                $installer->getTable('quote_address'),
                'customer_address_attribute',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Custom address attribute'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $this->updateDisplayCustomerGrid();
        }

        $installer->endSetup();
    }

    /**
     * Not display type date in customer grid when version magento >= 2.4.0
     */
    public function updateDisplayCustomerGrid()
    {
        $this->customerGridNotDisplay->updateDisplayCustomerGrid();
    }
}
