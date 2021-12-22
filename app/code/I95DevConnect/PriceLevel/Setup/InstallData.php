<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Setup class to create custom tables
 */
class InstallData implements InstallDataInterface
{

    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    public $customerSetupFactory;

    /**
     * @var IndexerRegistry
     */
    public $indexerRegistry;
    public $objectManagerInterface;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param IndexerRegistry $indexerRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        IndexerRegistry $indexerRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface
    ) {

        $this->customerSetupFactory = $customerSetupFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->objectManagerInterface = $objectManagerInterface;
    }

    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup *
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->insertOnDuplicate(
            $setup->getTable('i95dev_entity'),
            [
                [
                    'entity_name' => 'Price Level',
                    'entity_code' => 'pricelevel',
                    'sort_order'=>12,
                    'support_for_inbound' => true,
                    'support_for_outbound' => false
                ]
            ]
        );

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'pricelevel',
            [
            'type' => 'varchar',
            'label' => 'Price Level',
            'input' => 'text',
            'required' => false,
            'sort_order' => 3,
            'visible' => false,
            'system' => false,
                ]
        );

        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();
        $setup->endSetup();
    }
}
