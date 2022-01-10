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
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Url;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Helper\FileType;
use Mageplaza\CustomForm\Model\Form as CustomFormModel;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\Collection;

/**
 * Class FormCreator
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab
 */
class FormCreator extends Generic implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_CustomForm::form/form-creator.phtml';

    /**
     * @var Url
     */
    protected $frontendUrl;

    /**
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var FileType
     */
    protected $fileTypeHelper;

    /**
     * @var Responses
     */
    protected $responses;

    /**
     * @var Collection
     */
    protected $responsesCollection;

    /**
     * FormCreator constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Url $frontendUrl
     * @param Config $wysiwygConfig
     * @param Data $helperData
     * @param FileType $fileTypeHelper
     * @param Collection $responsesCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Url $frontendUrl,
        Config $wysiwygConfig,
        Data $helperData,
        FileType $fileTypeHelper,
        Collection $responsesCollection,
        array $data = []
    ) {
        $this->frontendUrl         = $frontendUrl;
        $this->wysiwygConfig       = $wysiwygConfig;
        $this->helperData          = $helperData;
        $this->fileTypeHelper      = $fileTypeHelper;
        $this->responsesCollection = $responsesCollection;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('form_');
        $form->setFieldNameSuffix('form');

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getCustomFormData()
    {
        /** @var CustomFormModel $customForm */
        $customForm = $this->_coreRegistry->registry('mageplaza_custom_form_form');

        return $customForm->getCustomForm() ?: '{}';
    }

    /**
     * @return int
     */
    public function getCustomFormResponses()
    {
        /** @var CustomFormModel $customForm */
        $customForm = $this->_coreRegistry->registry('mageplaza_custom_form_form');
        $count      = 0;
        if ($customForm && $customForm->getId()) {
            $responses  = $this->responsesCollection->addFieldToFilter('form_id', $customForm->getId());
            $count      = $responses->getSize();
        }

        return $count;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Form Creator');
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

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->frontendUrl->getUrl(
            'mpcustomform/preview',
            ['_nosid' => true, 'form_key' => $this->getFormKey()]
        );
    }

    /**
     * @return DataObject
     */
    public function getWysiwygConfig()
    {
        $formatConfig                = $this->wysiwygConfig->getConfig()->getData();
        $formatConfig['add_widgets'] = false;
        foreach ($formatConfig['plugins'] as $key => $plugin) {
            if ($plugin['name'] === 'magentowidget') {
                array_splice($formatConfig['plugins'], $key, 1);
            }
        }

        return $formatConfig;
    }

    /**
     * @return array
     */
    public function getTinymceConfig()
    {
        $config = [
            'tinymce4' => [
                'toolbar'     => 'formatselect | bold italic underline | alignleft aligncenter alignright | '
                    . 'bullist numlist | link table charmap',
                'plugins'     => implode(' ', [
                    'advlist',
                    'autolink',
                    'lists',
                    'link',
                    'charmap',
                    'media',
                    'noneditable',
                    'table',
                    'contextmenu',
                    'paste',
                    'code',
                    'help',
                    'table'
                ]),
                'content_css' => $this->_assetRepo->getUrl('mage/adminhtml/wysiwyg/tiny_mce/themes/ui.css')
            ]
        ];

        return $config;
    }

    /**
     * @return bool
     */
    public function isNewVersion()
    {
        return (int) $this->helperData->versionCompare('2.3.0');
    }

    /**
     * @return mixed
     */
    public function getGoogleMapApi()
    {
        return $this->helperData->getGoogleMapApi();
    }

    /**
     * @return array
     */
    public function getMimeTypes()
    {
        return $this->fileTypeHelper->getMimeTypes();
    }

    /**
     * @return array
     */
    public function getFieldTypes()
    {
        return [
            'text'      => __('Text'),
            'textarea'  => __('Textarea'),
            'dropdown'  => __('Dropdown'),
            'checkbox'  => __('Checkbox'),
            'radio'     => __('Radio'),
            'datetime'  => __('Date & Time'),
            'grid'      => __('Grid'),
            'upload'    => __('Upload File'),
            'agreement' => __('Policy Checkbox'),
            'rating'    => __('Star Rating'),
            'map'       => __('Google Map'),
            'html'      => __('HTML'),
        ];
    }

    /**
     * @return string
     */
    public function getFormCreatorData()
    {
        $data = [
            'formData'       => Data::jsonDecode($this->getCustomFormData()),
            'responses'      => $this->getCustomFormResponses(),
            'wysiwygConfig'  => $this->getWysiwygConfig(),
            'tinymceConfig'  => $this->getTinymceConfig(),
            'isNewVersion'   => $this->isNewVersion(),
            'mpGoogleMapKey' => $this->getGoogleMapApi(),
            'mimeTypes'      => $this->getMimeTypes(),
            'fieldTypes'     => $this->getFieldTypes()
        ];

        return Data::jsonEncode($data);
    }
}
