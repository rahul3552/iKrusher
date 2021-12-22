<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * UpgradeData class for configurable product
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var
     */
    public $categorySetupFactory;

    /**
     * UpgradeData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $categorySetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }
    
    /**
     * {}
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'variant_id',
                [
                'label' => __('Variant Id'),
                'input' => 'text',
                'required' => false,
                'sort_order' => 42,
                'visible' => false,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false
                    ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.5') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $productEntityTypeId = $categorySetup->getEntityTypeId('catalog_product');
            $id = $categorySetup->getAttributeId($productEntityTypeId, 'variant_id');
            $categorySetup->updateAttribute($productEntityTypeId, $id, 'is_used_in_grid', 1);
            $categorySetup->updateAttribute($productEntityTypeId, $id, 'is_visible_in_grid', 1);
            $categorySetup->updateAttribute($productEntityTypeId, $id, 'is_filterable_in_grid', 1);
            $categorySetup->updateAttribute($productEntityTypeId, $id, 'is_searchable_in_grid', 1);
            $categorySetup->updateAttribute($productEntityTypeId, $id, 'apply_to', 'simple');
        }

        $setup->endSetup();
    }
}
