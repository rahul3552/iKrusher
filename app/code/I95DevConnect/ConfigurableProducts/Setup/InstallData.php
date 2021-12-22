<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Class for add custom attributes
 */
class InstallData implements InstallDataInterface
{

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     */
    private $attributeSet;

    /**
     * @var Magento\Framework\App\State $appState
     */
    private $appState;

    /**
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     * @param \Magento\Framework\App\State $appState
     * @param LoggerInterface $logger
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
        \Magento\Framework\App\State $appState,
        LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSet = $attributeSet;
        $this->logger = $logger;
    }

    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $this->appState->setAreaCode('global');
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                'critical'
            );
        }
        $setup->startSetup();
        $setup->getConnection()->insertOnDuplicate(
            $setup->getTable('i95dev_entity'),
            [
                    [
                    'entity_name' => 'Configurable Product',
                    'entity_code' => 'configurableproduct',
                    'sort_order'=>1.1,
                    'support_for_inbound' => true,
                    'support_for_outbound' => false
                    ]
                ]
        );
        
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        
        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'parentsku', [
            'group' => 'General',
            'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
            'frontend' => '',
            'label' => 'ParentSku',
            'required' => false,
            'input' => 'hidden',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_WEBSITE,
            'visible' => false,
            'user_defined' => true,
            'apply_to' => '',
            'visible_on_front' => false,
            'used_in_product_listing' => false,
        ]);
        $setup->endSetup();
    }
}
