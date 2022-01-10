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

namespace Mageplaza\CustomForm\Block\Adminhtml\Responses\Edit;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Data\Form as FormData;
use Mageplaza\CustomForm\Model\Form as FormModel;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Registry;
use Mageplaza\CustomForm\Helper\Data;
use Mageplaza\CustomForm\Model\FormFactory as CustomFormFactory;
use Mageplaza\CustomForm\Model\ResourceModel\Form as CustomFormResource;
use Mageplaza\CustomForm\Model\Responses;

/**
 * Class Form
 * @package Mageplaza\CustomForm\Block\Adminhtml\Responses\Edit
 */
class Form extends Generic
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_CustomForm::response.phtml';

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var View
     */
    protected $customerHelper;

    /**
     * @var File
     */
    protected $file;

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
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param File $file
     * @param FormFactory $formFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param View $customerHelper
     * @param CustomFormFactory $customFormFactory
     * @param CustomFormResource $customFormResource
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        File $file,
        FormFactory $formFactory,
        CustomerRepositoryInterface $customerRepository,
        View $customerHelper,
        CustomFormFactory $customFormFactory,
        CustomFormResource $customFormResource,
        Data $helperData,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerHelper     = $customerHelper;
        $this->file               = $file;
        $this->customFormFactory  = $customFormFactory;
        $this->customFormResource = $customFormResource;
        $this->helperData         = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var FormData $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'      => 'edit_form',
                    'action'  => $this->getData('action'),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return Responses
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getResponse()
    {
        /** @var Responses $response */
        $response     = $this->_coreRegistry->registry('mageplaza_custom_form_response');
        $customerName = __('Guest');
        $email        = '';
        if ($response->getCustomerId()) {
            $customer     = $this->customerRepository->getById($response->getCustomerId());
            $customerName = $this->customerHelper->getCustomerName($customer);
            $email        = $customer->getEmail();
        }
        $response->addData([
            'customer_name'  => $customerName,
            'customer_email' => $email
        ]);

        return $response;
    }

    /**
     * @param int|string $storeId
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreView($storeId)
    {
        $store = $this->_storeManager->getStore($storeId);
        if (!$store->getId()) {
            $deleted = __(' [deleted]');

            return nl2br($this->getOrder()->getStoreName()) . $deleted;
        }
        $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];

        return implode('<br/>', $name);
    }

    /**
     * @param int|string $formId
     *
     * @return FormModel
     */
    public function getCustomForm($formId)
    {
        $customForm = $this->customFormFactory->create();
        $this->customFormResource->load($customForm, $formId);

        return $customForm;
    }

    /**
     * @param Responses $response
     *
     * @return array
     */
    public function prepareCustomFormData($response)
    {
        $responseData   = Data::jsonDecode($response->getFormData());
        $customForm     = $this->getCustomForm($response->getFormId());
        $customFormData = Data::jsonDecode($customForm->getCustomForm());
        if (!empty($customFormData) && is_array($customFormData)) {
            foreach ($customFormData as $pageId => &$page) {
                if (!empty($page['field_groups']) && is_array($page['field_groups'])) {
                    foreach ($page['field_groups'] as $fieldGroupId => &$fieldGroup) {
                        if (!empty($fieldGroup['fields']) && is_array($fieldGroup['fields'])) {
                            foreach ($fieldGroup['fields'] as $fieldId => &$field) {
                                $field['chose_value'] =
                                    isset($responseData[$pageId]['fieldGroups'][$fieldGroupId]['fields'][$fieldId]) ?
                                        $responseData[$pageId]['fieldGroups'][$fieldGroupId]['fields'][$fieldId] :
                                        null;
                            }
                            unset($field);
                        }
                    }
                    unset($fieldGroup);
                }
            }
            unset($page);
        }

        return $customFormData;
    }

    /**
     * @return mixed
     */
    public function getGoogleMapApi()
    {
        return $this->helperData->getGoogleMapApi();
    }

    /**
     * @param string $currentTime
     *
     * @return string
     * @throws Exception
     */
    public function getDateFormat($currentTime)
    {
        return $this->helperData->getDateFormat($currentTime, 'Y-m-d H:i:s');
    }

    /**
     * @param array $fieldData
     *
     * @return string
     */
    public function renderFieldHtml($fieldData)
    {
        $html = '<label class="admin__field-label"><span>' . $fieldData['title'] . '</span></label>';

        $tooltipClass = $fieldData['tooltip'] ? '_with-tooltip' : '';
        $html         .= '<div class="admin__field-control ' . $tooltipClass . '">';
        if ($fieldData['tooltip']) {
            $html .= '<div class="field-tooltip toggle">
                        <span class="field-tooltip-action action-help"
                        tabindex="0"
                        data-toggle="dropdown"
                        data-mage-init=\'{"dropdown":{}}\'
                        aria-haspopup="true" aria-expanded="false" role="button"></span>
                        <div class="field-tooltip-content" data-target="dropdown" >' . $fieldData['tooltip'] . '</div>
                    </div>';
        }
        if (empty($fieldData['chose_value'])) {
            $fieldData['chose_value'] = '';
        }
        switch ($fieldData['type']) {
            case 'text':
            case 'datetime':
                $html .= '<input class="admin__control-text" type="text" value="'
                    . $fieldData['chose_value'] . '" readonly>';
                break;
            case 'textarea':
                $html .= '<textarea class="admin__control-textarea" readonly>'
                    . $fieldData['chose_value']
                    . '</textarea>';
                break;
            case 'dropdown':
                if (empty($fieldData['options']) || !is_array($fieldData['options'])) {
                    break;
                }
                $html .= '<select class="admin__control-select">';
                foreach ($fieldData['options'] as $option) {
                    $disable = (!isset($fieldData['chose_value']) || $fieldData['chose_value'] !== $option['value']) ?
                        'disabled' : '';
                    $html    .= '<option value="' . $option['value'] . '" ' . $disable . '>'
                        . $option['label'] .
                        '</option>';
                }
                $html .= '</select>';
                break;
            case 'checkbox':
                if (empty($fieldData['options']) || !is_array($fieldData['options'])) {
                    break;
                }
                $html   .= '<div class="field-val-wrapper">';
                $count  = 0;
                $length = count($fieldData['options']);
                foreach ($fieldData['options'] as $option) {
                    $rowCount    = !empty($fieldData['row_count']) ? $fieldData['row_count'] : 1;
                    $countPerRow = ceil($length / $rowCount);
                    if ($count % $countPerRow === 0) {
                        $html .= '<div>';
                    }
                    $checked = (!empty($fieldData['chose_value'])
                        && in_array($option['value'], (array) $fieldData['chose_value'], true))
                        ? 'checked' : '';
                    $html    .= '<input type="checkbox" value="' . $option['value'] . '" ' . $checked . ' disabled>' .
                        '<label>' . $option['label'] . '</label>';
                    $count++;
                    if ($count % $countPerRow === 0 || $count === $length) {
                        $html .= '</div>';
                    }
                }
                $html .= '</div>';
                break;
            case 'radio':
                if (empty($fieldData['options']) || !is_array($fieldData['options'])) {
                    break;
                }
                $html   .= '<div class="field-val-wrapper">';
                $count  = 0;
                $length = count($fieldData['options']);
                foreach ($fieldData['options'] as $option) {
                    $rowCount    = !empty($fieldData['row_count']) ? $fieldData['row_count'] : 1;
                    $countPerRow = ceil($length / $rowCount);
                    if ($count % $countPerRow === 0) {
                        $html .= '<div>';
                    }
                    $checked = (isset($fieldData['chose_value']) && $fieldData['chose_value'] === $option['value']) ?
                        'checked' : '';
                    $html    .= '<input type="radio" value="' . $option['value'] . '" ' . $checked . ' disabled>' .
                        '<label>' . $option['label'] . '</label>';
                    $count++;
                    if ($count % $countPerRow === 0 || $count === $length) {
                        $html .= '</div>';
                    }
                }
                $html .= '</div>';
                break;
            case 'grid':
                if (empty($fieldData['rows']) || empty($fieldData['columns'])) {
                    break;
                }
                $html .= '<div class="field-val-wrapper">';
                $html .= '<table class="admin__table-primary"><thead><tr><td></td>';
                foreach ((array) $fieldData['columns'] as $column) {
                    $html .= '<td>' . $column . '</td>';
                }
                $html .= '</tr></thead><tbody>';
                foreach ((array) $fieldData['rows'] as $rowId => $row) {
                    $html .= '<tr>';
                    $html .= '<td>' . $row . '</td>';
                    foreach ((array) $fieldData['columns'] as $columnId => $column) {
                        $checked = '';
                        if ($fieldData['chose_value']
                            && in_array($rowId . '-' . $columnId, (array) $fieldData['chose_value'], true)) {
                            $checked = 'checked';
                        }
                        $html .= '<td><input value="' . $rowId . '-' . $columnId . '"
                        type="' . $fieldData['select_type'] . '" ' . $checked . ' disabled></td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
                $html .= '</div>';
                break;
            case 'upload':
                $html     .= '<div class="field-val-wrapper">';
                $path     = $fieldData['chose_value'];
                $fileName = $this->file->getPathInfo($path)['basename'];

                $html .= '<a href="' . $this->getUrl('mpcustomform/viewfile/index', ['file' => base64_encode($path)])
                    . '">' .
                    $fileName .
                    '</a>';
                $html .= '</div>';
                break;
            case 'agreement':
                $html          .= '<div class="field-val-wrapper">';
                $checked       = empty($fieldData['chose_value']) ? '' : 'checked';
                $html          .= '<input type="checkbox" ' . $checked . ' disabled>';
                $checkboxLabel = $fieldData['checkbox_label'];
                $anchorText    = $fieldData['anchor_text'];
                $anchorType    = $fieldData['anchor_type'];
                if ($anchorType === 'redirect') {
                    $url    = $fieldData['url'];
                    $anchor = '<a href="' . $url . '" target="_blank">' . $anchorText . '</a>';
                    $html   .= '<label>' . str_replace('{anchor}', $anchor, $checkboxLabel) . '</label>';
                } else {
                    $modalContent = $fieldData['agreement_content'];
                    $modalTitle   = $fieldData['agreement_title'];
                    $anchor       = '<a href="#" class="open-agreement-modal">' . $anchorText . '</a>';
                    $html         .= '<label>' . str_replace('{anchor}', $anchor, $checkboxLabel) . '</label>';
                    $html         .= '<div class="anchor-modal" style="">' .
                        '<input type="hidden" class="modal-title" value="' . $modalTitle . '">' .
                        '<input type="hidden" class="modal-content" value="' . $modalContent . '"></div>';
                }
                $html .= '</div>';
                break;
            case 'rating':
                $numberStar = $fieldData['number_star'];
                $choseVal   = empty($fieldData['chose_value']) ? 0 : (int) $fieldData['chose_value'];
                $ratingHtml = '<div class="rating-stars text-center"><ul class="stars">';
                for ($i = 1; $i <= $numberStar; $i++) {
                    $selectedClass = $i <= $choseVal ? ' selected' : '';
                    $ratingHtml    .= '<li class="star' . $selectedClass . '" >' .
                        '<i class="fa fa-star fa-fw"></i>' .
                        '</li>';
                }
                $ratingHtml .= '</ul></div>';
                $html       .= $ratingHtml;
                break;
            case 'map':
                $choseValue = !empty($fieldData['chose_value']) ? $fieldData['chose_value'] : [];
                $position   = !empty($choseValue['position']) ? $choseValue['position'] : $fieldData['position'];
                $zoom       = !empty($choseValue['zoom']) ? $choseValue['zoom'] : $fieldData['zoom'];
                $html       .= '<input type="hidden" class="map-zoom" value="' . $zoom . '">' .
                    '<input type="hidden" class="map-position" value="' . $position . '">' .
                    '<div class="google-map" style="width: 100%;height: 300px"></div>';
                break;
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get URL to edit the customer.
     *
     * @param int $customerId
     *
     * @return string
     */
    public function getCustomerViewUrl($customerId)
    {
        if (!$customerId) {
            return '';
        }

        return $this->getUrl('customer/index/edit', ['id' => $customerId]);
    }
}
