<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Class InstallData
 * @package Aheadworks\OneStepCheckout\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var ConfigSetup
     */
    private $configSetup;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @param ConfigSetup $configSetup
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        ConfigSetup $configSetup,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->configSetup = $configSetup;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup
            ->addAttribute(
                'quote',
                'aw_order_note',
                ['type' => Table::TYPE_TEXT, 'required' => false]
            )->addAttribute(
                'quote',
                'aw_delivery_date_from',
                ['type' => Table::TYPE_TIMESTAMP, 'required' => false]
            )->addAttribute(
                'quote',
                'aw_delivery_date_to',
                ['type' => Table::TYPE_TIMESTAMP, 'required' => false]
            )->addAttribute(
                'quote',
                'aw_delivery_date',
                ['type' => Table::TYPE_TIMESTAMP, 'required' => false]
            );

        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup
            ->addAttribute(
                'order',
                'aw_order_note',
                ['type' => Table::TYPE_TEXT, 'required' => false]
            )->addAttribute(
                'order',
                'aw_delivery_date_from',
                ['type' => Table::TYPE_TIMESTAMP, 'required' => false]
            )->addAttribute(
                'order',
                'aw_delivery_date_to',
                ['type' => Table::TYPE_TIMESTAMP, 'required' => false]
            )->addAttribute(
                'order',
                'aw_delivery_date',
                ['type' => Table::TYPE_TIMESTAMP, 'required' => false]
            );

        $setup->getConnection(InstallSchema::SALES_CONNECTION_NAME)->addColumn(
            $setup->getTable('sales_order_grid', InstallSchema::SALES_CONNECTION_NAME),
            'aw_delivery_date_from',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'comment' => 'AW OSC Delivery Time From'
            ]
        );
        $setup->getConnection(InstallSchema::SALES_CONNECTION_NAME)->addColumn(
            $setup->getTable('sales_order_grid', InstallSchema::SALES_CONNECTION_NAME),
            'aw_delivery_date_to',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'comment' => 'AW OSC Delivery Time To'
            ]
        );
        $setup->getConnection(InstallSchema::SALES_CONNECTION_NAME)->addColumn(
            $setup->getTable('sales_order_grid', InstallSchema::SALES_CONNECTION_NAME),
            'aw_delivery_date',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'comment' => 'AW OSC Delivery Date'
            ]
        );

        $this->configSetup
            ->restoreToDefault($setup, 'sales/totals_sort/customerbalance')
            ->restoreToDefault($setup, 'sales/totals_sort/giftcardaccount')
            ->restoreToDefault($setup, 'sales/totals_sort/reward');

        $setup->endSetup();
    }
}
