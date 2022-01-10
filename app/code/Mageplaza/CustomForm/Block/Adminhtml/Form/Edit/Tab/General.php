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
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\System\Store;
use Mageplaza\CustomForm\Model\Form as ModelForm;

/**
 * Class General
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    /**
     * @var Enabledisable
     */
    protected $enabledisable;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    protected $_objectConverter;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Enabledisable $enableDisable
     * @param Store $systemStore
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $enableDisable,
        Store $systemStore,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        array $data = []
    ) {
        $this->enabledisable          = $enableDisable;
        $this->systemStore            = $systemStore;
        $this->_groupRepository       = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter       = $objectConverter;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var ModelForm $customForm */
        $customForm = $this->_coreRegistry->registry('mageplaza_custom_form_form');
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('form_');
        $form->setFieldNameSuffix('form');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('General'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);
        $fieldset->addField('identifier', 'text', [
            'name'     => 'identifier',
            'label'    => __('Identifier'),
            'title'    => __('Identifier'),
            'required' => true
        ]);
        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->enabledisable->toOptionArray()
        ]);
        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()
                ->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name'     => 'store_ids',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);
        }
        $customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems();
        $fieldset->addField('customer_group_ids', 'multiselect', [
            'name'     => 'customer_group_ids[]',
            'label'    => __('Customer Groups'),
            'title'    => __('Customer Groups'),
            'required' => true,
            'values'   => $this->_objectConverter->toOptionArray($customerGroups, 'id', 'code')
        ]);

        $dateFormat = $this->_localeDate->getDateFormat();
        $fieldset->addField('valid_from_date', 'date', [
            'name'         => 'valid_from_date',
            'label'        => __('Valid From Date'),
            'title'        => __('Valid From Date'),
            'input_format' => DateTime::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ]);
        $fieldset->addField('valid_to_date', 'date', [
            'name'         => 'valid_to_date',
            'label'        => __('Valid To Date'),
            'title'        => __('Valid To Date'),
            'input_format' => DateTime::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ]);

        if ($formId = $customForm->getId()) {
            $snippetFieldset = $form->addFieldset('snippet_fieldset', [
                'legend' => __('Snippet'),
                'class'  => 'fieldset-wide'
            ]);
            $tab             = '&nbsp&nbsp&nbsp&nbsp';
            $str             = __('Use the following code to display this form at any places you want.');
            $cmsText         = '<div>';
            $cmsText         .= '<strong>' . $str . '</strong><br/><br/>';
            $cmsText         .= '{{block class="Mageplaza\CustomForm\Block\CustomForm" identifier="'
                . $customForm->getIdentifier()
                . '" template="Mageplaza_CustomForm::custom-form.phtml"}}';
            $cmsText         .= '</div>';
            $snippetFieldset->addField('cms_snippet', 'note', [
                'name'  => 'cms_snippet',
                'label' => __('CMS'),
                'title' => __('CMS'),
                'text'  => $cmsText,
            ]);
            $phtmlText = '<div>';
            $phtmlText .= '<strong>' . $str . '</strong><br/><br/>';
            $phtmlText .= $this->_escaper
                    ->escapeHtml(
                        '<?php echo $block->getLayout()->createBlock(\Mageplaza\CustomForm\Block\CustomForm::class)'
                    ) . '<br/>';
            $phtmlText .= $this->_escaper
                ->escapeHtml('->setIdentifier("' . $customForm->getIdentifier() . '")->toHtml();?>');
            $phtmlText .= '</div>';
            $snippetFieldset->addField('phtml_snippet', 'note', [
                'name'  => 'phtml_snippet',
                'label' => __('Phtml'),
                'title' => __('Phtml'),
                'text'  => $phtmlText,
            ]);
            $layoutText = '<div>';
            $layoutText .= '<strong>' . $str . '</strong><br/><br/>';
            $layoutText .= $this->_escaper
                    ->escapeHtml('<block class="Mageplaza\CustomForm\Block\CustomForm" name="mpcustomform">') . '<br/>';
            $layoutText .= $tab . $this->_escaper->escapeHtml('<arguments>') . '<br/>';
            $layoutText .= $tab . $tab . $this->_escaper
                    ->escapeHtml(
                        '<argument name="identifier" xsi:type="string">' . $customForm->getIdentifier() . '</argument>'
                    ) . '<br/>';
            $layoutText .= $tab . $this->_escaper->escapeHtml('</arguments>') . '<br/>';
            $layoutText .= $this->_escaper->escapeHtml('</block>');
            $layoutText .= '</div>';
            $snippetFieldset->addField('layout_snippet', 'note', [
                'name'  => 'layout_snippet',
                'label' => __('Layout'),
                'title' => __('Layout'),
                'text'  => $layoutText
            ]);
        }

        $form->addValues($customForm->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
