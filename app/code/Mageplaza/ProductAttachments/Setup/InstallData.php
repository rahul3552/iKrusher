<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\ProductAttachments\Helper\Data;
use Zend_Validate_Exception;

/**
 * Class InstallData
 * @package Mageplaza\ProductAttachments\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * @var CoreHelper
     */
    private $_helperData;

    /**
     * InstallData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param CoreHelper $helperData
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CoreHelper $helperData
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_helperData = $helperData;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $configData = [];
        $value = $this->_helperData->serialize($configData);
        $data = [
            'scope' => 'default',
            'scope_id' => 0,
            'path' => Data::CONFIG_MODULE_PATH . '/general/manage_icon/icons',
            'value' => $value,
        ];
        $setup->getConnection()
            ->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);

        $this->_installEavAttribute($setup);
    }

    /**
     * @param $setup
     *
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    private function _installEavAttribute($setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attachment location attribute to the product eav/attribute
         */
        $eavSetup->removeAttribute(Product::ENTITY, 'mp_attachments_location');
        $eavSetup->addAttribute(
            Product::ENTITY,
            'mp_attachments_location',
            [
                'group' => 'Product Details',
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Attachment Location',
                'input' => 'text',
                'class' => 'mp-attachment-location',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );
    }
}
