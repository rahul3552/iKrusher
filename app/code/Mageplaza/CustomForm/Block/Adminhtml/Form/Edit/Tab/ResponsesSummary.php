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

use DateTime;
use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Mageplaza\CustomForm\Helper\Data;
use \Mageplaza\CustomForm\Model\Form as ModelForm;
use Mageplaza\CustomForm\Model\ResourceModel\Form as CustomFormResource;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\Collection;
use Mageplaza\CustomForm\Model\ResourceModel\Responses\CollectionFactory as ResponsesCollectionFactory;
use Mageplaza\CustomForm\Model\Responses;

/**
 * Class General
 * @package Mageplaza\CustomForm\Block\Adminhtml\Form\Edit\Tab
 */
class ResponsesSummary extends Generic implements TabInterface
{
    const CHART_COLOR = [
        '20a8d8',
        '6610f2',
        '6f42c1',
        'e83e8c',
        'f86c6b',
        'f8cb00',
        'ffc107',
        '4dbd74',
        '20c997',
        '17a2b8'
    ];

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_CustomForm::form/responses-summary.phtml';

    /**
     * @var File
     */
    protected $file;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResponsesCollectionFactory
     */
    protected $responsesCollectionFactory;

    /**
     * @var CustomFormResource
     */
    protected $customFormResource;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * ResponsesSummary constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param File $file
     * @param FormFactory $formFactory
     * @param MessageManagerInterface $messageManager
     * @param ResponsesCollectionFactory $responsesCollectionFactory
     * @param CustomFormResource $customFormResource
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        File $file,
        FormFactory $formFactory,
        MessageManagerInterface $messageManager,
        ResponsesCollectionFactory $responsesCollectionFactory,
        CustomFormResource $customFormResource,
        Data $helperData,
        array $data = []
    ) {
        $this->file                       = $file;
        $this->messageManager             = $messageManager;
        $this->responsesCollectionFactory = $responsesCollectionFactory;
        $this->customFormResource         = $customFormResource;
        $this->helperData                 = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     * @throws Exception
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('form_');
        $form->setFieldNameSuffix('form');
        $this->setForm($form);
        $this->prepareResponsesSummary();

        return parent::_prepareForm();
    }

    /**
     * @return ModelForm
     */
    public function getCustomForm()
    {
        return $this->_coreRegistry->registry('mageplaza_custom_form_form');
    }

    /**
     * @throws Exception
     */
    public function prepareResponsesSummary()
    {
        /** @var ModelForm $customForm */
        $customForm = $this->getCustomForm();
        if (!$customForm->getId()) {
            return;
        }
        $lastUpdate = $customForm->getLastResponsesUpdate();
        $customForm->setLastResponsesUpdate(new DateTime());
        /** @var Collection $responsesCollection */
        $responsesCollection = $this->responsesCollectionFactory->create()
            ->addFieldToFilter('form_id', $customForm->getId())
            ->addFieldToFilter('created_at', ['from' => $lastUpdate]);
        try {
            if (!$responsesCollection->getSize()) {
                return;
            }
        } catch (Exception $exception) {
            return;
        }
        $responsesSummary = Data::jsonDecode($customForm->getResponsesSummary() ?: '{}');
        /** @var Responses $response */
        foreach ($responsesCollection as $response) {
            $responseData = Data::jsonDecode($response->getFormData() ?: '{}');
            foreach ((array) $responseData as $pageId => $page) {
                if (empty($page['fieldGroups']) || !is_array($page['fieldGroups'])) {
                    continue;
                }
                foreach ((array) $page['fieldGroups'] as $fieldGroupId => $fieldGroup) {
                    if (empty($fieldGroup['fields']) || !is_array($page['fieldGroups'])) {
                        continue;
                    }
                    foreach ((array) $fieldGroup['fields'] as $fieldId => $field) {
                        $responsesSummary[$pageId][$fieldGroupId][$fieldId][$response->getId()] = $field;
                    }
                }
            }
        }
        $customForm->setResponsesSummary(Data::jsonEncode($responsesSummary));
        try {
            $this->customFormResource->save($customForm);
        } catch (Exception $e) {
            $this->_logger->critical($e);
            $this->messageManager->addErrorMessage(__('Something went wrong while preparing response summary'));
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function prepareResponsesSummaryData()
    {
        $customForm       = $this->getCustomForm();
        $customFormData   = Data::jsonDecode($customForm->getCustomForm() ?: '{}');
        $responsesSummary = Data::jsonDecode($customForm->getResponsesSummary() ?: '{}');
        if (!is_array($customFormData)) {
            return [];
        }
        foreach ($customFormData as $pageId => &$page) {
            if (empty($page['field_groups']) || !is_array($page['field_groups'])) {
                continue;
            }
            foreach ($page['field_groups'] as $fieldGroupId => &$fieldGroup) {
                if (empty($fieldGroup['fields']) || !is_array($fieldGroup['fields'])) {
                    continue;
                }
                foreach ($fieldGroup['fields'] as $fieldId => &$field) {
                    if (!empty($responsesSummary[$pageId][$fieldGroupId][$fieldId])) {
                        $field['responses_summary'] = $this->prepareFieldData(
                            $field,
                            $responsesSummary[$pageId][$fieldGroupId][$fieldId]
                        );
                    }
                }
                unset($field);
            }
            unset($fieldGroup);
        }
        unset($page);

        return $customFormData;
    }

    /**
     * @param int $index
     *
     * @return string
     */
    protected function getColor($index)
    {
        $division = (int) ($index / 10);
        $modulus  = $index % 10;

        $decColor = hexdec(self::CHART_COLOR[$modulus]) + $division;

        return dechex($decColor);
    }

    /**
     * @param array $field
     * @param array $values
     * @param array $fieldResponses
     *
     * @return string
     * @throws Exception
     */
    private function prepareDateTimeField($field, $values, $fieldResponses)
    {
        $result = '';
        switch ($field['dateTimeType']) {
            case 'datetime-local':
                $data = [];
                foreach ($values as $item) {
                    $date                                                    = new DateTime($item);
                    $data[$date->format('Ym')]['label']                      = $date->format('M Y');
                    $data[$date->format('Ym')]['days'][$date->format('d')][] = $date->format('H:i');
                }
                krsort($data);
                $result = '<div class="responses-total">' . $this->getResponseLabel(count($fieldResponses)) . '</div>';
                $result .= '<div class="mp-date-responses-container">';
                foreach ($data as $dateResponse) {
                    $result .= '<div class="mp-year-moth-row">';
                    $result .= '<div class="mp-year-moth-label">' . $dateResponse['label'] . '</div>';
                    $result .= '<div class="mp-day-container datetime">';
                    if (empty($dateResponse['days']) || !is_array($dateResponse['days'])) {
                        continue;
                    }
                    $sortedDays = $dateResponse['days'];
                    krsort($sortedDays);
                    foreach ($sortedDays as $day => $times) {
                        $result .= '<div class="mp-day">';
                        $result .= '<span>' . $day . '</span>';
                        $times  = array_count_values($times);
                        krsort($times);
                        $result .= '<div class="mp-times-col"><div class="mp-times-container">';
                        foreach ($times as $time => $count) {
                            $result .= '<div class="mp-time-response ' . ($count > 1 ? 'with-count' : '')
                                . '"><span>' . $time . '</span>';
                            if ($count > 1) {
                                $result .= '<div class="count">' . $count . '</div>';
                            }
                            $result .= '</div>';
                        }
                        $result .= '</div></div>';
                        $result .= '</div>';
                    }
                    $result .= '</div>';
                    $result .= '</div>';
                }
                $result .= '</div>';
                break;
            case 'date':
                $values = array_count_values($values);
                $data   = [];
                foreach ($values as $day => $responseCount) {
                    $date                                                   = new DateTime($day);
                    $data[$date->format('Y-m')]['label']                    = $date->format('M Y');
                    $data[$date->format('Y-m')]['days'][$date->format('d')] = $responseCount;
                }
                krsort($data);
                $result = '<div class="mp-date-responses-container">';
                foreach ($data as $dateResponse) {
                    $result .= '<div class="mp-year-moth-row">';
                    $result .= '<div class="mp-year-moth-label">' . $dateResponse['label'] . '</div>';
                    $result .= '<div class="mp-day-container date">';
                    if (empty($dateResponse['days']) || !is_array($dateResponse['days'])) {
                        continue;
                    }
                    $sortedDays = $dateResponse['days'];
                    krsort($sortedDays);
                    foreach ($sortedDays as $day => $count) {
                        $result .= '<div class="mp-day ' . ($count > 1 ? 'with-count' : '') . '">';
                        $result .= '<span>' . $day . '</span>';

                        if ($count > 1) {
                            $result .= '<div class="count">' . $count . '</div>';
                        }
                        $result .= '</div>';
                    }
                    $result .= '</div>';
                    $result .= '</div>';
                }
                $result .= '</div>';
                break;
            case 'time':
                $values = array_count_values($values);
                $data   = [];
                foreach ($values as $time => $responseCount) {
                    $time                                      = new DateTime($time);
                    $hour                                      = $time->format('h');
                    $data[$hour]['label']                      = $hour;
                    $data[$hour]['times'][$time->format('Hi')] = [
                        'time'  => $time->format('H:i'),
                        'count' => $responseCount
                    ];
                }
                krsort($data);
                $result = '<div class="mp-date-responses-container">';
                foreach ($data as $hour => $hourResponse) {
                    $result .= '<div class="mp-year-moth-row">';
                    $result .= '<div class="mp-year-moth-label">' . $hour . ' : _ _</div>';
                    $result .= '<div class="mp-day-container date">';
                    if (empty($dateResponse['times']) || !is_array($dateResponse['times'])) {
                        continue;
                    }
                    $sortedTimes = $hourResponse['times'];
                    krsort($sortedTimes);
                    foreach ($sortedTimes as $time) {
                        $result .= '<div class="mp-day ' . ($time['count'] > 1 ? 'with-count' : '') . '">';
                        $result .= '<span>' . $time['time'] . '</span>';

                        if ($time['count'] > 1) {
                            $result .= '<div class="count">' . $time['count'] . '</div>';
                        }
                        $result .= '</div>';
                    }
                    $result .= '</div>';
                    $result .= '</div>';
                }
                $result .= '</div>';
                break;
        }

        return $result;
    }

    /**
     * @param array $field
     * @param array $fieldResponses
     *
     * @return string
     * @throws Exception
     */
    public function prepareFieldData($field, $fieldResponses)
    {
        $result = '';
        switch ($field['type']) {
            case 'text':
            case 'textarea':
                $values = array_filter($fieldResponses);
                if (empty($values)) {
                    break;
                }
                $result = '<div class="responses-total">' . $this->getResponseLabel(count($values)) . '</div>';
                $result .= '<ul>';
                foreach ($values as $response) {
                    $result .= '<li><span>' . $this->escapeHtml($response) . '</span></li>';
                }
                $result .= '</ul>';

                break;
            case 'radio':
            case 'dropdown':
                $values = array_filter($fieldResponses);
                if (empty($values)) {
                    break;
                }
                $data      = array_count_values($values);
                $chartData = [];
                $count     = 0;
                foreach ((array) $field['options'] as $option) {
                    $chartData['labels'][]           = $this->escapeQuote($option['label']);
                    $chartData['backgroundColors'][] = '#' . $this->getColor($count);
                    $chartData['data'][]             =
                        empty($data[$option['value']]) ? 0 : (int) $data[$option['value']];
                    $count++;
                }
                $chartData['total'] = count($fieldResponses);
                $result             = '<div class="responses-total">' . $this->getResponseLabel($chartData['total'])
                    . '</div>';
                $result             .=
                    '<canvas height="100" class="pie-chart" data-mage-init=\'{"mpResponsesSummary":{"type":"'
                    . $field['type'] . '","chartData":' . Data::jsonEncode($chartData) . '}}\'></canvas>';
                break;
            case 'checkbox':
                $values = [];
                foreach ((array) $fieldResponses as $item) {
                    $values = array_merge($values, $item);
                }
                if (empty($values)) {
                    break;
                }
                $data      = array_count_values($values);
                $chartData = [];
                $count     = 0;
                foreach ((array) $field['options'] as $option) {
                    $chartData['labels'][]           = $this->escapeQuote($option['label']);
                    $chartData['backgroundColors'][] = '#' . $this->getColor($count);
                    $chartData['data'][]             =
                        empty($data[$option['value']]) ? 0 : (int) $data[$option['value']];
                    $count++;
                }
                $chartData['total'] = count($values);
                $result             = '<div class="responses-total">'
                    . $this->getResponseLabel(count(array_filter($fieldResponses)))
                    . '</div>';
                $result             .=
                    '<canvas height="100" class="horizontal-bar-chart" data-mage-init=\'{"mpResponsesSummary":{"type":"'
                    . $field['type'] . '","chartData":'
                    . $this->escapeHtml(Data::jsonEncode($chartData)) . '}}\'></canvas>';
                break;
            case 'datetime':
                $values = array_filter($fieldResponses);
                if (empty($values) || !is_array($values)) {
                    break;
                }
                $result .= $this->prepareDateTimeField($field, $values, $fieldResponses);
                break;
            case 'grid':
                $values = [];
                foreach ((array) $fieldResponses as $item) {
                    $values = array_merge($values, array_values($item));
                }
                if (empty($values)) {
                    break;
                }
                $values = array_count_values($values);
                $labels = array_values($field['rows']);
                $data   = [];
                $count  = 0;
                foreach ((array) $field['columns'] as $columnId => $column) {
                    $columnData = [
                        'label'           => $this->escapeHtml($this->escapeQuote($column)),
                        'backgroundColor' => '#' . $this->getColor($count)
                    ];
                    foreach ((array) $field['rows'] as $rowId => $row) {
                        $index                = $rowId . '-' . $columnId;
                        $columnData['data'][] = isset($values[$index])
                            ? (int) $values[$rowId . '-' . $columnId] : 0;
                    }
                    $data[] = $columnData;
                    $count++;
                }
                $chartData = [
                    'labels'   => $labels,
                    'datasets' => $data,
                    'total'    => count($fieldResponses)
                ];
                $result    = '<div class="responses-total">' . $this->getResponseLabel($chartData['total']) . '</div>';
                $result    .=
                    '<canvas height="100" class="horizontal-bar-chart" data-mage-init=\'{"mpResponsesSummary":{"type":"'
                    . $field['type'] . '","chartData":' . Data::jsonEncode($chartData) . '}}\'></canvas>';
                break;
            case 'upload':
                $values = array_filter($fieldResponses);
                if (empty($values)) {
                    break;
                }
                $result = '<div class="responses-total">' . $this->getResponseLabel(count($values)) . '</div>';
                $result .= '<ul>';
                foreach ($values as $response) {
                    $fileName = $this->file->getPathInfo($response)['basename'];
                    $url      = $this->getUrl('mpcustomform/viewfile/index', ['file' => base64_encode($response)]);
                    $result   .= '<li><a href="' . $url . '">' . $fileName . '</a></li>';
                }
                $result .= '</ul>';

                break;
            case 'rating':
                $values = array_filter($fieldResponses);
                if (empty($values)) {
                    break;
                }
                $responsesTotal = count($values);
                $values         = array_count_values($values);
                $max            = $field['number_star'];
                if ($max <= 0) {
                    break;
                }
                $totalPoint = 0;
                foreach ($values as $starNum => $count) {
                    $totalPoint += (int) $starNum * $count;
                }
                $ratingSummary = round($totalPoint / $responsesTotal, 1);
                $percent       = round(($totalPoint / $responsesTotal) * 100 / $max, 2);

                $result =
                    '<div class="mp-review-statistic col-mp mp-7 mp-md-7 mp-sm-7 mp-xs-12">' .
                    '   <div class="summary-points">' .
                    '       <span class="summary-points-text">' . $ratingSummary . '</span>' .
                    '       <div class="product-reviews-summary short">' .
                    '           <div class="rating-summary">' .
                    '               <div style="width: ' . ($max * 28.9) . 'px" title="'
                    . $percent . '%" class="rating-result stars-' . $max . '">' .
                    '                   <span style="width: ' . ((int) $percent) . '%">' .
                    '                       <span>' . $percent . '%</span>' .
                    '                   </span>' .
                    '               </div>' .
                    '           </div>' .
                    '           <div class="reviews-actions">' . $this->getResponseLabel($responsesTotal) .
                    '           </div>' .
                    '       </div>' .
                    '   </div>' .
                    '</div>';

                $result .= '<div class="mp-review-details col-mp mp-4 mp-md-4 mp-sm-4 mp-xs-12">';
                for ($i = $max; $i > 0; $i--) {
                    $width  = empty($values[$i]) ? 0 : $values[$i] / $responsesTotal;
                    $result .=
                        '<div class="mp-review-details-' . $i . 'stars">' .
                        '    <div class="stars-title">' .
                        ($i === 1 ? __('%1 Star', $i) : __('%1 Stars', $i)) .
                        '    </div>' .
                        '    <div class="stars-process">' .
                        '        <div class="stars-process-active" style="width: ' . ceil($width * 100) . '%"></div>' .
                        '    </div>' .
                        '    <span>' . round($width * 100, 2) . '%</span>' .
                        '</div>';
                }
                $result .= '</div>';

                $result .=
                    '<style>.rating-summary .rating-result.stars-' . $max . ':before{content:';
                for ($i = 0; $i < $max; $i++) {
                    $result .= "'\\f005'";
                }
                $result .= ';}</style>';
                break;
            case 'map':
                $values    = [];
                $addresses = [];
                foreach ((array) $fieldResponses as $response) {
                    if (!empty($response['position'])) {
                        $values[] = $response['position'];
                    }
                    if (!empty($response['address'])) {
                        $addresses[] = $response['address'];
                    }
                }
                if (empty($values)) {
                    break;
                }
                $result .= '<div class="responses-total">' . $this->getResponseLabel(count($values)) . '</div>';
                $result .= '<ul>';
                foreach ($addresses as $address) {
                    $result .= '<li><span>' . $this->escapeHtml($address) . '</span></li>';
                }
                $result    .= '</ul>';
                $chartData = array_count_values($values);
                $result    .=
                    '<div style="height: 500px" class="google-map-responses"'
                    . ' data-mage-init=\'{"mpResponsesSummary":{"mpGoogleMapKey": "'
                    . $this->helperData->getGoogleMapApi() . '","position":"' . $field['position'] . '","zoom":"'
                    . $field['zoom'] . '","type":"' . $field['type'] . '","chartData":' . Data::jsonEncode($chartData)
                    . '}}\'></div>';
                break;
        }

        return $result;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Response Summary');
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
     * @param int $count
     *
     * @return Phrase
     */
    protected function getResponseLabel($count)
    {
        return $count > 1 ? __('%1 responses', $count) : __('%1 response', $count);
    }
}
