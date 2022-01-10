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

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Config\Source\Page as CmsPage;
use Magento\Config\Model\Config\Source\Email\Identity as EmailIdentity;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\CustomForm\Model\Config\Source\AfterSubmitForm;
use Mageplaza\CustomForm\Model\Config\Source\FormStyle;
use Mageplaza\CustomForm\Model\Config\Source\PopupType;

/**
 * Class FormBehavior
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab
 */
class FormBehavior extends Generic implements TabInterface
{
    /**
     * @var CmsPage
     */
    protected $cmsPage;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var EmailIdentity
     */
    protected $emailIdentity;

    /**
     * @var FormStyle
     */
    protected $formStyle;

    /**
     * @var PopupType
     */
    protected $popupType;

    /**
     * @var AfterSubmitForm
     */
    protected $afterSubmitForm;

    /**
     * EmailNotification constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CmsPage $cmsPage
     * @param Yesno $yesno
     * @param EmailIdentity $emailIdentity
     * @param FormStyle $formStyle
     * @param PopupType $popupType
     * @param AfterSubmitForm $afterSubmitForm
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CmsPage $cmsPage,
        Yesno $yesno,
        EmailIdentity $emailIdentity,
        FormStyle $formStyle,
        PopupType $popupType,
        AfterSubmitForm $afterSubmitForm,
        array $data = []
    ) {
        $this->cmsPage = $cmsPage;
        $this->yesno = $yesno;
        $this->emailIdentity = $emailIdentity;
        $this->formStyle = $formStyle;
        $this->popupType = $popupType;
        $this->afterSubmitForm = $afterSubmitForm;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\CustomForm\Model\Form $customForm */
        $customForm = $this->_coreRegistry->registry('mageplaza_custom_form_form');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('form_');
        $form->setFieldNameSuffix('form');

        $fieldset = $form->addFieldset('form_behavior_fieldset', [
            'legend' => __('Form Behavior'),
            'class' => 'fieldset-wide'
        ]);
        $formStyle = $fieldset->addField('form_style', 'select', [
            'name' => 'form_style',
            'label' => __('Form Style'),
            'title' => __('Form Style'),
            'values' => $this->formStyle->toOptionArray(),
        ]);
        $buttonText = $fieldset->addField('fb_button_text', 'text', [
            'name' => 'fb_button_text',
            'label' => __('Button Text'),
            'title' => __('Button Text'),
        ]);
        $popupType = $fieldset->addField('popup_type', 'select', [
            'name' => 'popup_type',
            'label' => __('Popup Type'),
            'title' => __('Popup Type'),
            'values' => $this->popupType->toOptionArray(),
        ]);
        $fieldset->addField('custom_css', 'textarea', [
            'name' => 'custom_css',
            'label' => __('Custom CSS'),
            'title' => __('Custom CSS'),
        ]);

        $actionsFieldset = $form->addFieldset('fb_actions_fieldset', [
            'legend' => __('Actions'),
            'class' => 'fieldset-wide'
        ]);
        $afterSubmitForm = $actionsFieldset->addField('action_after_submit', 'select', [
            'name' => 'action_after_submit',
            'label' => __('After Submitting Form'),
            'title' => __('After Submitting Form'),
            'values' => $this->afterSubmitForm->toOptionArray(),
        ]);
        $pageUrl = $actionsFieldset->addField('page_url', 'text', [
            'name' => 'page_url',
            'label' => __('Redirect URL'),
            'title' => __('Redirect URL'),
            'class' => 'validate-url',
            'required' => true,
        ]);
        $cmsPage = $actionsFieldset->addField('cms_page', 'select', [
            'name' => 'cms_page',
            'label' => __('CMS Page'),
            'title' => __('CMS Page'),
            'values' => $this->cmsPage->toOptionArray(),
        ]);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Dependence::class)
                ->addFieldMap($formStyle->getHtmlId(), $formStyle->getName())
                ->addFieldMap($buttonText->getHtmlId(), $buttonText->getName())
                ->addFieldMap($popupType->getHtmlId(), $popupType->getName())
                ->addFieldMap($afterSubmitForm->getHtmlId(), $afterSubmitForm->getName())
                ->addFieldMap($pageUrl->getHtmlId(), $pageUrl->getName())
                ->addFieldMap($cmsPage->getHtmlId(), $cmsPage->getName())
                ->addFieldDependence($buttonText->getName(), $formStyle->getName(), 'popup')
                ->addFieldDependence($popupType->getName(), $formStyle->getName(), 'popup')
                ->addFieldDependence($pageUrl->getName(), $afterSubmitForm->getName(), 'url')
                ->addFieldDependence($cmsPage->getName(), $afterSubmitForm->getName(), 'cms')
        );

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
        return __('Form Behavior');
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
