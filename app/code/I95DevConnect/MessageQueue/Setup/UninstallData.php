<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Setup;

/**
 * Interface for handling data removal during module uninstall
 *
 * @api
 */
class UninstallData implements \Magento\Framework\Setup\UninstallInterface
{
    public $customerSetupFactory;

    /**
     * UninstallData constructor.
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Setup\Module\DataSetup $dataSetup
     * @param \I95DevConnect\MessageQueue\Model\EntityList $entityList
     * @param \Magento\Eav\Setup\EavSetupFactory $categorySetupFactory
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Setup\Module\DataSetup $dataSetup,
        \I95DevConnect\MessageQueue\Model\EntityList $entityList,
        \Magento\Eav\Setup\EavSetupFactory $categorySetupFactory
    ) {

        $this->categorySetupFactory = $categorySetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->dataSetup = $dataSetup;
        $this->entityList = $entityList;
    }

    /**
     * Invoked when remove-data flag is set during module uninstall
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $setup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->dataSetup]);
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'update_by'
        );

        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->dataSetup]);
        $categorySetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'targetproductstatus'
        );

        $categorySetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'update_by'
        );

        $setup->endSetup();
    }
}
