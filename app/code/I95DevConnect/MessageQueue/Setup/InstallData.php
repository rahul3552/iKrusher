<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Interface for handling data adding during module install
 */
class InstallData implements InstallDataInterface
{
    const LABEL = 'label';
    const INPUT = 'input';
    const REQUIRED = 'required';
    const SORT_ORDER = 'sort_order';
    const VISIBLE = 'visible';
    const SYSTEM = 'system';
    const IS_USED_IN_GRID = 'is_used_in_grid';
    const IS_VISIBLE_IN_GRID = 'is_visible_in_grid';
    const IS_FILTERABLE_IN_GRID = 'is_filterable_in_grid';
    const IS_SEARCHABLE_IN_GRID = 'is_searchable_in_grid';
    const UPDATE_BY = 'update_by';

    private $customerSetupFactory;
    private $categorySetupFactory;
    public $objectManagerInterface;

    /**
     * InstallData constructor.
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Eav\Setup\EavSetupFactory $categorySetupFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Eav\Setup\EavSetupFactory $categorySetupFactory,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->objectManagerInterface = $objectManagerInterface;
    }

    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $targetCustomerExists = $customerSetup->getAttribute(Customer::ENTITY, 'target_customer_id');
        if (!$targetCustomerExists) {
            $customerSetup->addAttribute(
                Customer::ENTITY,
                'target_customer_id',
                [
                    self::LABEL => 'ERP Customer ID',
                    self::INPUT => 'text',
                    'type' => 'text',
                    self::REQUIRED => false,
                    self::SORT_ORDER => 30,
                    self::VISIBLE => false,
                    self::SYSTEM => false,
                    self::IS_USED_IN_GRID => true,
                    self::IS_VISIBLE_IN_GRID => true,
                    self::IS_FILTERABLE_IN_GRID => true,
                    self::IS_SEARCHABLE_IN_GRID => true
                ]
            );
        }

        $originExists = $customerSetup->getAttribute(Customer::ENTITY, 'origin');
        if (!$originExists) {
            $customerSetup->addAttribute(
                Customer::ENTITY,
                'origin',
                [
                    self::LABEL => 'Origin',
                    self::INPUT => 'text',
                    'type' => 'text',
                    self::REQUIRED => false,
                    self::SORT_ORDER => 40,
                    self::VISIBLE => false,
                    self::SYSTEM => false,
                    self::IS_USED_IN_GRID => true,
                    self::IS_VISIBLE_IN_GRID => true,
                    self::IS_FILTERABLE_IN_GRID => true,
                    self::IS_SEARCHABLE_IN_GRID => true
                ]
            );
        }

        $updateByStatus = $customerSetup->getAttribute(Customer::ENTITY, self::UPDATE_BY);
        if (!$updateByStatus) {
            $customerSetup->addAttribute(
                Customer::ENTITY,
                self::UPDATE_BY,
                [
                    self::LABEL => self::UPDATE_BY,
                    self::INPUT => 'text',
                    self::REQUIRED => false,
                    self::SORT_ORDER => 40,
                    self::VISIBLE => false,
                    self::SYSTEM => false
                ]
            );
        }

        $addressTargetId = $customerSetup->getAttribute('customer_address', 'target_address_id');
        if (!$addressTargetId) {
            $customerSetup->addAttribute(
                'customer_address',
                'target_address_id',
                [
                    self::LABEL => 'ERP Address ID',
                    self::INPUT => 'text',
                    self::REQUIRED => false,
                    self::SORT_ORDER => 40,
                    self::VISIBLE => false,
                    self::SYSTEM => false,
                    'frontend_input' => 'hidden',
                ]
            );
        }

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $StatusExists = $categorySetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'targetproductstatus');
        if (!$StatusExists) {
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'targetproductstatus',
                [
                    self::LABEL => __('Target Product Status'),
                    self::INPUT => 'text',
                    self::REQUIRED => false,
                    self::SORT_ORDER => 40,
                    self::VISIBLE => false,
                    self::SYSTEM => false,
                    self::IS_USED_IN_GRID => true,
                    self::IS_VISIBLE_IN_GRID => true,
                    self::IS_FILTERABLE_IN_GRID => true,
                    self::IS_SEARCHABLE_IN_GRID => true
                ]
            );
        }

        $updatedByExists = $categorySetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, self::UPDATE_BY);
        if (!$updatedByExists) {
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                self::UPDATE_BY,
                [
                    self::LABEL => self::UPDATE_BY,
                    self::INPUT => 'text',
                    self::REQUIRED => false,
                    self::SORT_ORDER => 40,
                    self::VISIBLE => false,
                    self::SYSTEM => false
                ]
            );
        }

        $setup->endSetup();
    }
}
