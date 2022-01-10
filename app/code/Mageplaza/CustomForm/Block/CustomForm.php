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

namespace Mageplaza\CustomForm\Block;

use Exception;
use Magento\Cms\Helper\Page as CmsPage;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\Store;
use Magento\Widget\Block\BlockInterface;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\Form as CustomFormModel;
use Mageplaza\CustomForm\Model\FormFactory as CustomFormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as CustomFormResource;

/**
 * Class CustomForm
 * @package Mageplaza\CustomForm\Block
 */
class CustomForm extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_CustomForm::custom-form.phtml';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var CmsPage
     */
    protected $cmsPage;

    /**
     * @var CustomFormFactory
     */
    protected $customFormFactory;

    /**
     * @var CustomFormResource
     */
    protected $customFormResource;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * CustomForm constructor.
     *
     * @param Template\Context $context
     * @param DateTime $date
     * @param CmsPage $cmsPage
     * @param CustomFormFactory $customFormFactory
     * @param CustomFormResource $customFormResource
     * @param FilterProvider $filterProvider
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        DateTime $date,
        CmsPage $cmsPage,
        CustomFormFactory $customFormFactory,
        CustomFormResource $customFormResource,
        FilterProvider $filterProvider,
        Data $helperData,
        array $data = []
    ) {
        $this->request            = $context->getRequest();
        $this->date               = $date;
        $this->cmsPage            = $cmsPage;
        $this->customFormFactory  = $customFormFactory;
        $this->customFormResource = $customFormResource;
        $this->helperData         = $helperData;
        $this->filterProvider     = $filterProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return CustomFormModel
     */
    public function loadCustomForm()
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = Store::DEFAULT_STORE_ID;
        }

        /** @var CustomFormModel $customForm */
        $customForm = $this->customFormFactory->create();
        $identifier = $this->getData('identifier');
        $customForm = $customForm->getFilterByStoreId($identifier, $storeId);

        return $customForm;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isValidForm()
    {
        /** @var CustomFormModel $customForm */
        $customForm     = $this->loadCustomForm();
        $isEnabled      = $customForm->getStatus();
        $validFromDate  = $customForm->getValidFromDate();
        $validToDate    = $customForm->getValidToDate();
        $storeIds       = explode(',', $customForm->getStoreIds());
        $timeStamp      = strtotime($this->date->date('Y-m-d'));
        $storeId        = $this->_storeManager->getStore()->getId();
        $isModuleEnable = $this->helperData->isEnabled($storeId);

        return $isEnabled && $isModuleEnable
            && (!$validFromDate || $timeStamp >= strtotime($validFromDate))
            && (!$validToDate || $timeStamp <= strtotime($validToDate))
            && (in_array($storeId, $storeIds, false) || in_array('0', $storeIds, false));
    }

    /**
     * @param bool $isArray
     *
     * @return array|mixed|string
     */
    public function getCustomFormData($isArray = false)
    {
        /** @var CustomFormModel $customForm */
        $customForm = $this->loadCustomForm();

        $customFormData = $customForm->getCustomForm();
        if ($isArray) {
            return $customFormData ? $this->helperData->jsDecode($customFormData) : [];
        }

        return $customFormData ?: '{}';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        $customForm = $this->loadCustomForm();
        switch ($customForm->getActionAfterSubmit()) {
            case 'url':
                return $customForm->getPageUrl();
            case 'cms':
                return $this->cmsPage->getPageUrl($customForm->getCmsPage());
            default:
                return '';
        }
    }

    /**
     * @return string
     */
    public function getFormName()
    {
        return $this->getData('name') ? rtrim(strtr(base64_encode($this->getData('name')), '+/', '__'), '=') : '';
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws Exception
     */
    public function getPageFilter($content)
    {
        return $this->filterProvider->getPageFilter()->filter((string) $content);
    }

    /**
     * @param string $elemId
     *
     * @return string
     * @throws Exception
     */
    public function getCustomFormJsLayout($elemId)
    {
        /** @var CustomFormModel $customForm */
        $customForm        = $this->loadCustomForm();
        $customFormData    = $this->getCustomFormData(true);
        $emailAddressField = $customForm->getAutoResEmailAddress();
        $deps              = [];
        $pages             = [];

        if ($emailAddressField) {
            list($pageId, $fieldGroupId, $fieldId) = explode('-', $emailAddressField);
            if (isset($customFormData[$pageId]['field_groups'][$fieldGroupId]['fields'][$fieldId])) {
                $class                                                                                        = $customFormData[$pageId]['field_groups'][$fieldGroupId]['fields'][$fieldId]['validate_class'];
                $customFormData[$pageId]['field_groups'][$fieldGroupId]['fields'][$fieldId]['validate_class'] =
                    $class . ' validate-email';
            }
        }

        $count = 0;
        foreach ($customFormData as $page) {
            $pageData = [
                'component'   => 'Mageplaza_CustomForm/js/form/page',
                'config'      => [
                    '_id'               => $page['_id'],
                    'sortOrder'         => $count,
                    'title'             => isset($page['title']) ? $page['title'] : '',
                    'description'       => isset($page['description'])
                        ? $this->getPageFilter($page['description']) : '',
                    'smButtonText'      => isset($page['sm_button_text']) ? $page['sm_button_text'] : '',
                    'smButtonClass'     => isset($page['sm_button_class']) ? $page['sm_button_class'] : '',
                    'submitUrl'         => $this->getUrl('mpcustomform/customform/submit'),
                    'viewsUrl'          => $this->getUrl('mpcustomform/customform/views'),
                    'actionAfterSubmit' => $customForm->getActionAfterSubmit(),
                    'identifier'        => $this->getIdentifier(),
                    'formName'          => $this->getFormName(),
                    'formStyle'         => $customForm->getFormStyle(),
                    'formId'            => $elemId
                ],
                'provider'    => 'customForm-' . $elemId . '.steps.page-' . $page['_id'] . '-provider',
                'children'    => [],
                'sortOrder'   => $count,
                'displayArea' => 'pages',
                'params'      => [
                    'elemId' => $elemId,
                    'formId' => $this->getData('id')
                ],
            ];
            if (isset($page['field_groups']) && is_array($page['field_groups'])) {
                foreach ($page['field_groups'] as $fieldGroup) {
                    $fieldGroupData = [
                        'component' => 'uiComponent',
                        'template'  => 'Mageplaza_CustomForm/form/field-group',
                        'config'    => [
                            '_id'         => $fieldGroup['_id'],
                            'title'       => isset($fieldGroup['title']) ? $fieldGroup['title'] : '',
                            'description' => isset($fieldGroup['description']) ? $fieldGroup['description'] : '',
                        ],
                        'children'  => []
                    ];
                    if (isset($fieldGroup['fields']) && is_array($fieldGroup['fields'])) {
                        foreach ($fieldGroup['fields'] as $field) {
                            $fieldGroupData['children']['field-' . $field['_id']] =
                                $this->getFieldJsLayout($field, $elemId, $page, $fieldGroup);
                        }
                    }
                    $pageData['children']['field-group-' . $fieldGroup['_id']] = $fieldGroupData;
                }
            }
            $pages['page-' . $page['_id']]               = $pageData;
            $pages['page-' . $page['_id'] . '-provider'] = [
                'component'   => 'uiComponent',
                'displayArea' => 'pageProvider'
            ];
            $deps[]                                      = 'customForm-' . $elemId . '.steps.' . 'page-' . $page['_id'];
            $count++;
        }
        $data = [
            'components' => [
                'customForm-' . $elemId => [
                    'component' => 'Mageplaza_CustomForm/js/custom-form',
                    'config'    => [
                        'formId'                => $elemId,
                        'popupType'             => $customForm->getPopupType(),
                        'template'              => 'Mageplaza_CustomForm/custom-form',
                        'customerGroupIds'      => $customForm->getCustomerGroupIds(),
                        'checkCustomerGroupUrl' => $this->getUrl('mpcustomform/customform/checkcustomergroup'),
                        'isPreview'             => $this->request->getFullActionName() === 'mpcustomform_preview_index'
                    ],
                    'children'  => [
                        'progressBar' => [
                            'component'   => 'Mageplaza_CustomForm/js/progress-bar',
                            'displayArea' => 'progressBar',
                            'config'      => [
                                'deps'     => $deps,
                                'formId'   => $elemId,
                                'formName' => $this->getFormName(),
                            ]
                        ],
                        'steps'       => [
                            'component'   => 'uiComponent',
                            'displayArea' => 'steps',
                            'formId'      => $elemId,
                            'children'    => $pages
                        ]
                    ]
                ],
                'mpCustomFormProvider'  => [
                    'component' => 'uiComponent',
                ]
            ]
        ];

        return Data::jsonEncode($data);
    }

    /**
     * @param array $field
     * @param string $elemId
     * @param array $page
     * @param array $fieldGroup
     *
     * @return array
     */
    private function getFieldJsLayout($field, $elemId, $page, $fieldGroup)
    {
        $columnClass = (!empty($field['width']) && $field['width'] === '50')
            ? ' mp-2column ' : ' mp-1column ';
        $fieldData   = [
            'component'   => 'Mageplaza_CustomForm/js/form/field/' . $field['type'],
            'provider'    => 'customForm-' . $elemId . '.steps.page-' . $page['_id'] . '-provider',
            'displayArea' => 'fields',
            'scope'       => 'field',
            'dataScope'   => 'form.pages.' . $page['_id'] . '.fieldGroups.' . $fieldGroup['_id']
                . '.fields.' . $field['_id'],
            'config'      => [
                '_id'               => $field['_id'],
                'pageName'          => 'page-' . $page['_id'],
                'fieldType'         => $field['type'],
                'additionalClasses' => (isset($field['additional_class'])
                        ? $field['additional_class'] : '') . $columnClass,
                'label'             => isset($field['title']) ? $field['title'] : '',
                'isRequired'        => isset($field['is_required']) ? $field['is_required'] : '',
                'width'             => isset($field['width']) ? $field['width'] : '',
                'depends'           => isset($field['depends']) ? $field['depends'] : '',
                'template'          => 'ui/form/field',
                'notice'            => isset($field['notice']) ? $field['notice'] : '',
                'validation'        => [
                    'required-entry' => isset($field['is_required']) && $field['is_required'],
                ],
            ],
        ];
        if (!empty($field['validate_class'])) {
            $validateClasses = explode(' ', $field['validate_class']);
            $validateClasses = array_unique(array_filter($validateClasses));
            foreach ($validateClasses as $class) {
                $fieldData['config']['validation'][$class] = true;
            }
        }

        if (isset($field['tooltip']) && $field['tooltip']) {
            $fieldData['config']['tooltip'] = ['description' => $field['tooltip']];
        }
        switch ($field['type']) {
            case 'dropdown':
                $fieldData['config']['options']     = isset($field['options'])
                    ? array_values($field['options'])
                    : '';
                $fieldData['config']['optionsData'] = isset($field['options']) ? $field['options'] : '';
                $fieldData['config']['default']     = isset($field['default']) ? $field['default'] : '';
                break;
            case 'datetime':
                $fieldData['config']['options']['mpDateTimeType']   = isset($field['dateTimeType'])
                    ? $field['dateTimeType'] : '';
                $fieldData['config']['additionalClasses']           .= ' date';
                $fieldData['config']['validation']['validate-date'] = true;
                break;
            case 'checkbox':
                $fieldData['config']['options']     = isset($field['options']) ? array_values($field['options']) : [];
                $fieldData['config']['countPerRow'] =
                    isset($field['row_count'], $field['options']) && $field['row_count']
                        ? ceil(count($field['options']) / $field['row_count'])
                        : '';
                $fieldData['config']['optionsData'] = isset($field['options']) ? $field['options'] : '';
                if (isset($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $option) {
                        if (isset($option['checked'])) {
                            $fieldData['config']['default'][] = $option['value'];
                        }
                    }
                }
                break;
            case 'radio':
                $fieldData['config']['options']     = isset($field['options']) ? array_values($field['options']) : [];
                $fieldData['config']['countPerRow'] =
                    isset($field['row_count'], $field['options']) && $field['row_count']
                        ? ceil(count($field['options']) / $field['row_count'])
                        : '';
                $fieldData['config']['optionsData'] = isset($field['options']) ? $field['options'] : '';
                if (isset($field['checked'])) {
                    $fieldData['config']['default'] = $field['checked'];
                }
                break;
            case 'grid':
                $this->getGridFieldJsLayout($field, $fieldData);
                break;
            case 'upload':
                $fieldData['config']['maxFileSize'] = isset($field['file_size']) && $field['file_size']
                    ? (int) $field['file_size'] : false;
                if (isset($field['file_type_allow'])) {
                    $fileTypeAllow                            = array_filter(array_map(
                        'trim',
                        explode(',', $field['file_type_allow'])
                    ));
                    $fieldData['config']['allowedExtensions'] = !empty($fileTypeAllow) ? $fileTypeAllow : false;
                }
                $fieldData['config']['fileInputName'] = $page['_id'] . '-' . $fieldGroup['_id'] . '-' . $field['_id'];
                break;
            case 'rating':
                $fieldData['config']['numberStar'] = isset($field['number_star']) ? $field['number_star'] : '';
                $fieldData['config']['default']    = isset($field['default']) ? $field['default'] : '';
                break;
            case 'map':
                $fieldData['config']['zoom']           = isset($field['zoom']) ? $field['zoom'] : '';
                $fieldData['config']['position']       = isset($field['position']) ? $field['position'] : '';
                $fieldData['config']['address']        = isset($field['address']) ? $field['address'] : '';
                $fieldData['config']['mpGoogleMapKey'] = $this->helperData->getGoogleMapApi();
                break;
            case 'agreement':
                $fieldData['config']['checkboxLabel']    = isset($field['checkbox_label'])
                    ? $field['checkbox_label'] : '';
                $fieldData['config']['anchorText']       = isset($field['anchor_text']) ? $field['anchor_text'] : '';
                $fieldData['config']['anchorType']       = $field['anchor_type'];
                $fieldData['config']['url']              = isset($field['url']) ? $field['url'] : '';
                $fieldData['config']['agreementTitle']   = isset($field['agreement_title'])
                    ? $field['agreement_title'] : '';
                $fieldData['config']['agreementContent'] = isset($field['agreement_content'])
                    ? $field['agreement_content'] : '';
                break;
            case 'html':
                $fieldData['config']['html'] = isset($field['html']) ? $field['html'] : '';
                break;
        }

        return $fieldData;
    }

    /**
     * @param array $field
     * @param array $fieldData
     */
    protected function getGridFieldJsLayout($field, &$fieldData)
    {
        $fieldData['config']['selectType'] = $field['select_type'];
        $fieldData['config']['validation'] = [
            'required-entry-' . $field['_id'] => isset($field['is_required']) && $field['is_required']
        ];
        if (isset($field['rows']) && is_array($field['rows'])) {
            $gridOption  = [];
            $gridRows    = [];
            $gridColumns = [];
            $gridCount   = 0;
            foreach ((array) $field['rows'] as $rowKey => $row) {
                $gridRows[] = ['value' => $rowKey, 'title' => $row];
                foreach ((array) $field['columns'] as $columnKey => $column) {
                    if ($gridCount === 0) {
                        $gridColumns[] = ['value' => $columnKey, 'title' => $column];
                    }
                    $gridOption[] = $rowKey . '-' . $columnKey;
                }
                $gridCount++;
            }

            $fieldData['config']['rows']       = $gridRows;
            $fieldData['config']['columns']    = $gridColumns;
            $fieldData['config']['options']    = $gridOption;
            $fieldData['config']['selectType'] = isset($field['select_type']) ? $field['select_type'] : 'radio';
        }
        $gridDefault = [];
        if (isset($field['default']) && is_array($field['default'])) {
            foreach ($field['default'] as $key => $item) {
                if ($field['select_type'] === 'radio') {
                    $gridDefault[] = $key . '-' . $item;
                } elseif (is_array($item)) {
                    foreach ($item as $columnId) {
                        $gridDefault[] = $key . '-' . $columnId;
                    }
                }
            }
            $fieldData['config']['default'] = $gridDefault;
        }
    }
}
