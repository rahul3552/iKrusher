<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Store\Model\ResourceModel\Store\Collection;

/**
 * Class Options
 *
 * @package Bss\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Options
 */
class Options extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;

    /**
     * @var string
     */
    protected $_template = 'Bss_CustomerAttributes::customer/attribute/options.phtml';

    /**
     * @var \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    protected $_universalFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrCollecFactory
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrCollecFactory,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_attrOptionCollectionFactory = $attrCollecFactory;
        $this->_universalFactory = $universalFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Is true only for system attributes which use source model
     *
     * Option labels and position for such attributes are kept in source model and thus cannot be overridden
     *
     * @return bool
     */
    public function canManageOptionDefaultOnly()
    {
        $attribute = $this->getAttributeObject();
        return !$attribute->getCanManageOptionLabels() &&
            !$attribute->getIsUserDefined() &&
            $attribute->getSourceModel();
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $this->setData('stores', $this->_storeManager->getStores(true));
        }
        return $this->_getData('stores');
    }

    /**
     * Returns stores sorted by Sort Order
     *
     * @return array
     */
    public function getStoresSortedBySortOrder()
    {
        $stores = $this->getStores();
        if (is_array($stores)) {
            usort($stores, function ($storeA, $storeB) {
                if ($storeA->getSortOrder() == $storeB->getSortOrder()) {
                    return $storeA->getId() < $storeB->getId() ? -1 : 1;
                }
                return ($storeA->getSortOrder() < $storeB->getSortOrder()) ? -1 : 1;
            });
        }
        return $stores;
    }

    /**
     * Retrieve attribute option values if attribute input type select or multiselect
     *
     * @return array
     */
    public function getOptionValues()
    {
        $values = $this->_getData('option_values');
        if ($values === null) {
            $values = [];

            $attribute = $this->getAttributeObject();
            $optionCollection = $this->_getOptionValuesCollection($attribute);
            if ($optionCollection) {
                $values = $this->_prepareOptionValues($attribute, $optionCollection);
            }

            $this->setData('option_values', $values);
        }

        return $values;
    }

    /**
     * Prepare Options
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection
     * @return array
     */
    protected function _prepareOptionValues(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        $optionCollection
    ) {
        $type = $attribute->getFrontendInput();
        if ($type === 'select' || $type === 'multiselect' || $type === 'checkboxs' || $type === 'radio') {
            $defaultValues = explode(',', $attribute->getDefaultValue());
            $inputType = in_array($type, ['select', 'radio']) ? 'radio' : 'checkbox';
        } else {
            $defaultValues = [];
            $inputType = '';
        }

        $values = [];
        $isSystemAttribute = is_array($optionCollection);
        foreach ($optionCollection as $option) {
            $bunch = $isSystemAttribute ? $this->_prepareSystemAttributeOptionValues(
                $option,
                $inputType,
                $defaultValues
            ) : $this->_prepareUserDefinedAttributeOptionValues(
                $option,
                $inputType,
                $defaultValues
            );
            foreach ($bunch as $value) {
                $values[] = $this->dataObjectFactory->create()->addData($value);
            }
        }

        return $values;
    }

    /**
     * Retrieve option values collection
     *
     * It is represented by an array in case of system attribute
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    protected function _getOptionValuesCollection(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute)
    {
        if ($this->canManageOptionDefaultOnly()) {
            $options = $this->_universalFactory->create(
                $attribute->getSourceModel()
            )->setAttribute(
                $attribute
            )->getAllOptions();
            return $options;
        } else {
            return $this->_attrOptionCollectionFactory->create()->setAttributeFilter(
                $attribute->getId()
            )->setPositionOrder(
                'asc',
                true
            )->load();
        }
    }

    /**
     * Prepare option values of system attribute
     *
     * @param array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option $option
     * @param string $inputType
     * @param array $defaultValues
     * @param string $valuePrefix
     * @return array
     */
    protected function _prepareSystemAttributeOptionValues($option, $inputType, $defaultValues, $valuePrefix = '')
    {
        if (is_array($option['value'])) {
            $values = [];
            foreach ($option['value'] as $subOption) {
                $bunch = $this->_prepareSystemAttributeOptionValues(
                    $subOption,
                    $inputType,
                    $defaultValues,
                    $option['label'] . ' / '
                );
                $values[] = $bunch[0];
            }
            return $values;
        }

        $value['checked'] = in_array($option['value'], $defaultValues) ? 'checked="checked"' : '';
        $value['intype'] = $inputType;
        $value['id'] = $option['value'];
        $value['sort_order'] = 0;

        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            $value['store' . $storeId] = $storeId ==
                \Magento\Store\Model\Store::DEFAULT_STORE_ID ? $valuePrefix . $this->escapeHtml($option['label']) : '';
        }

        return [$value];
    }

    /**
     * Prepare option values of user defined attribute
     *
     * @param array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option $option
     * @param string $inputType
     * @param array $defaultValues
     * @return array
     */
    protected function _prepareUserDefinedAttributeOptionValues($option, $inputType, $defaultValues)
    {
        $optionId = $option->getId();

        $value['checked'] = in_array($optionId, $defaultValues) ? 'checked="checked"' : '';
        $value['intype'] = $inputType;
        $value['id'] = $optionId;
        $value['sort_order'] = $option->getSortOrder();

        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            $storeValues = $this->getStoreOptionValues($storeId);
            $value['store' . $storeId] = isset(
                $storeValues[$optionId]
            ) ? $this->escapeHtml(
                $storeValues[$optionId]
            ) : '';
        }

        return [$value];
    }

    /**
     * Retrieve attribute option values for given store id
     *
     * @param int $storeId
     * @return array
     */
    public function getStoreOptionValues($storeId)
    {
        $values = $this->getData('store_option_values_' . $storeId);
        if ($values === null) {
            $values = [];
            $valuesCollection = $this->_attrOptionCollectionFactory->create()->setAttributeFilter(
                $this->getAttributeObject()->getId()
            )->setStoreFilter(
                $storeId,
                false
            )->load();
            foreach ($valuesCollection as $item) {
                $values[$item->getId()] = $item->getValue();
            }
            $this->setData('store_option_values_' . $storeId, $values);
        }
        return $values;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected function getAttributeObject()
    {
        return $this->_registry->registry('entity_attribute');
    }
}
