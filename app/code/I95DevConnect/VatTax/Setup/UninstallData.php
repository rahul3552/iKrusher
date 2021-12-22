<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Setup;

/**
 * Interface for handling data removal during module uninstall
 *
 * @api
 */
class UninstallData implements \Magento\Framework\Setup\UninstallInterface
{
    public $customerSetupFactory;
    public $dataSetup;

    /**
     * UninstallData constructor.
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Setup\Module\DataSetup $dataSetup
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Setup\Module\DataSetup $dataSetup
    ) {
    
        $this->customerSetupFactory = $customerSetupFactory;
        $this->dataSetup = $dataSetup;
    }

    /**
     * Invoked when remove-data flag is set during module uninstall
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        
        $setup->startSetup();
        
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->dataSetup]);
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'tax_bus_posting_group'
        );
        
        $customerSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tax_product_posting_group'
        );
        
        $setup->endSetup();
    }
}
