<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Setup;

use Magento\Authorization\Model\RoleFactory;
use Magento\Authorization\Model\RulesFactory;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;

/* For get RoleType and UserType for create Role   */

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * RoleFactory
     *
     * @var roleFactory
     */
    private $roleFactory;

    /**
     * RulesFactory
     *
     * @var rulesFactory
     */
    private $rulesFactory;

    /**
     * config model
     * @var Config
     */
    private $configModel;
    public $objectManagerInterface;

    /**
     *
     * @var type orderSetupFactory
     */
    private $orderSetupFactory;

    /**
     * Init Constructor
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param RoleFactory $roleFactory
     * @param RulesFactory $rulesFactory
     * @param Config $configModel
     */
    public function __construct(
        RoleFactory $roleFactory,
        SalesSetupFactory $orderSetupFactory,
        RulesFactory $rulesFactory,
        Config $configModel,
        ObjectManagerInterface $objectManagerInterface,
        CustomerSetupFactory $customerSetupFactory
    ) {

        $this->customerSetupFactory = $customerSetupFactory;
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
        $this->configModel = $configModel;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->orderSetupFactory = $orderSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0.5', '<')) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerSetup->addAttribute(
                Customer::ENTITY,
                'net_terms_id',
                [
                'label' => 'NetTerms ID',
                'input' => 'text',
                'type' => 'text',
                'required' => false,
                'sort_order' => 30,
                'visible' => false,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false
                ]
            );

            $customerSetup->getEavConfig()->getAttribute('customer', 'net_terms_id')
                    ->setData('used_in_forms', ['adminhtml_customer'])
                    ->save();
        }
        $setup->endSetup();
    }
}
