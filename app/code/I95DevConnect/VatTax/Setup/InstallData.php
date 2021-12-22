<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class for handling data addition during module install
 */
class InstallData implements InstallDataInterface
{
    const ENTITY_NAME = 'entity_name';
    const ENTITY_CODE = 'entity_code';
    const SORT_ORDER = 'sort_order';
    const SUPPORT_FOR_INBOUND = 'support_for_inbound';
    const SUPPORT_FOR_OUTBOUND = 'support_for_outbound';

    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;
    public $objectManagerInterface;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    private $categorySetupFactory;

    /**
     * @var Magento\Framework\App\State $appState
     */
    private $appState;

    protected $logger;

    /**
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     * @param IndexerRegistry $indexerRegistry
     * @param AttributeSetFactory $attributeSetFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        IndexerRegistry $indexerRegistry,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Framework\App\State $appState,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->logger = $logger;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $this->appState->setAreaCode('global');
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->logger->debug($exception->getMessage());
        }

        $setup->startSetup();
        $this->entityList = $this->objectManagerInterface->get("\I95DevConnect\MessageQueue\Model\EntityList");
        $this->entityList->insertEntityToEntityList(
            [
                self::ENTITY_NAME => 'TaxBusPostingGroup',
                self::ENTITY_CODE => 'taxbuspostinggroup',
                self::SORT_ORDER=>15.1,
                self::SUPPORT_FOR_INBOUND => true,
                self::SUPPORT_FOR_OUTBOUND => false
            ]
        );

        $this->entityList->insertEntityToEntityList(
            [
                self::ENTITY_NAME => 'TaxProductPostingGroup',
                self::ENTITY_CODE => 'taxproductpostinggroup',
                self::SORT_ORDER=>15.2,
                self::SUPPORT_FOR_INBOUND => true,
                self::SUPPORT_FOR_OUTBOUND => false
            ]
        );

        $this->entityList->insertEntityToEntityList(
            [
                self::ENTITY_NAME => 'TaxPostingSetup',
                self::ENTITY_CODE => 'taxpostingsetup',
                self::SORT_ORDER=>15.3,
                self::SUPPORT_FOR_INBOUND => true,
                self::SUPPORT_FOR_OUTBOUND => false
            ]
        );

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'tax_bus_posting_group',
            [
                'label' => 'Tax Busuness Posting Group',
                'input' => 'text',
                'type' => 'text',
                'required' => false,
                self::SORT_ORDER => 30,
                'visible' => false,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false
            ]
        );

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tax_product_posting_group',
            [
                'label' => __('Tax Product Posting Group'),
                'input' => 'text',
                'required' => false,
                self::SORT_ORDER => 40,
                'visible' => false,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false
            ]
        );

        $setup->endSetup();
    }
}
