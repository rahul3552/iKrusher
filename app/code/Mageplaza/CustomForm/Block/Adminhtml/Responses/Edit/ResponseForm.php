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

/**
 * Class ResponseForm
 * @package Mageplaza\CustomForm\Block\Adminhtml\Responses\Edit
 */
class ResponseForm extends Form
{
    /**
     * @param array $fieldData
     *
     * @return string
     */
    public function renderFieldHtml($fieldData)
    {
        $html = '<td><strong>' . $fieldData['title'] . '</strong></td>';
        $html .= '<td>';

        if (empty($fieldData['chose_value'])) {
            $fieldData['chose_value'] = '';
        }

        switch ($fieldData['type']) {
            case 'text':
            case 'datetime':
            case 'textarea':
                $html .= '<div>' . $fieldData['chose_value'] . '</div>';
                break;
            case 'dropdown':
                if (empty($fieldData['options']) || !is_array($fieldData['options'])) {
                    break;
                }

                $html .= '<select>';
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
                $html  .= '';
                $count = 0;
                foreach ($fieldData['options'] as $option) {
                    $checked = (!empty($fieldData['chose_value'])
                        && in_array($option['value'], (array) $fieldData['chose_value'], true))
                        ? 'checked' : '';
                    $html    .= '<input type="checkbox" value="' . $option['value'] . '" ' . $checked
                        . (!$checked ? 'disabled' : '') . '>' .
                        '<label>' . $option['label'] . '</label>';
                    $count++;
                }
                break;
            case 'radio':
                if (empty($fieldData['options']) || !is_array($fieldData['options'])) {
                    break;
                }
                $html  .= '';
                $count = 0;
                foreach ($fieldData['options'] as $option) {
                    $checked = (isset($fieldData['chose_value']) && $fieldData['chose_value'] === $option['value']) ?
                        'checked' : '';
                    $html    .= '<input type="radio" value="' . $option['value'] . '" ' . $checked
                        . (!$checked ? 'disabled' : '') . '>' .
                        '<label>' . $option['label'] . '</label>';
                    $count++;
                }
                break;
            case 'grid':
                if (empty($fieldData['rows']) || empty($fieldData['columns'])) {
                    break;
                }

                $html .= '<div>';
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
                        $html .= '<td><input value="' . $rowId . '-' . $columnId . '" type="'
                            . $fieldData['select_type'] . '" ' . $checked . (!$checked ? 'disabled' : '') . '></td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
                $html .= '</div>';
                break;
            case 'upload':
                $html     .= '<div>';
                $path     = $fieldData['chose_value'];
                $fileName = $this->file->getPathInfo($path)['basename'];
                $html     .= '<div>' . $fileName . '</div>';
                $html     .= '</div>';
                break;
            case 'agreement':
                $html          .= '<div>';
                $checked       = empty($fieldData['chose_value']) ? '' : 'checked';
                $html          .= '<input type="checkbox" ' . $checked . '>';
                $checkboxLabel = $fieldData['checkbox_label'];
                $anchorText    = $fieldData['anchor_text'];
                $anchorType    = $fieldData['anchor_type'];

                if ($anchorType === 'redirect') {
                    $anchor = '<div>' . $anchorText . '</div>';
                    $html   .= '<label>' . str_replace('{anchor}', $anchor, $checkboxLabel) . '</label>';
                } else {
                    $modalContent = $fieldData['agreement_content'];
                    $modalTitle   = $fieldData['agreement_title'];
                    $anchor       = '<div>' . $anchorText . '</div>';
                    $html         .= '<label>' . str_replace('{anchor}', $anchor, $checkboxLabel) . '</label>';
                    $html         .= '<div class="anchor-modal" style="">' .
                        '<input type="hidden" value="' . $modalTitle . '">' .
                        '<input type="hidden" value="' . $modalContent . '"></div>';
                }
                $html .= '</div>';
                break;
            case 'rating':
                $numberStar = $fieldData['number_star'];
                $choseVal   = empty($fieldData['chose_value']) ? 0 : (int) $fieldData['chose_value'];
                $startVoted = 0;

                for ($i = 1; $i <= $numberStar; $i++) {
                    if ($i <= $choseVal) {
                        $startVoted++;
                    }
                }

                $html .= $startVoted . '/' . $numberStar;
                break;
            case 'map':
                $html .= '<div>' . __('Map') . '</div>';
                break;
        }

        $html .= '</td>';

        return $html;
    }
}
