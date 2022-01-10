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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Block\Adminhtml\System;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ProductAttachments\Helper\Data;

/**
 * Class IconManage
 * @package Mageplaza\ProductAttachments\Block\Adminhtml\System
 */
class IconManagement extends AbstractFieldArray
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_ProductAttachments::system/config/form/field/icon_management.phtml';

    /**
     * @var Factory
     */
    protected $_elementFactory;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * IconManage constructor.
     *
     * @param Context $context
     * @param Factory $elementFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        Data $helperData,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * Unset scope
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('file_type', ['label' => __('File Type'), 'class' => 'required-entry mpattachments-file-box']);
        $this->addColumn('file_icon', ['label' => __('Icon')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     *
     * @return string
     * @throws Exception
     */
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new LocalizedException(__('Wrong column name specified.'));
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);
        $hiddenInputName = $this->_getCellInputElementName('file_icon_hidden');
        if ($column['renderer']) {
            return $column['renderer']->setInputName(
                $inputName
            )->setInputId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setColumnName(
                $columnName
            )->setColumn(
                $column
            )->toHtml();
        }
        if ($columnName === 'file_icon') {
            return '<img id="' . $this->_getCellInputElementId('<%- _id %>', 'file_icon_img')
                . '" src="' . $this->helperData->getImageUrl('<%- ' . $columnName . ' %>') . '">'
                . '<input type="file" id="' . $this->_getCellInputElementId('<%- _id %>', $columnName)
                . '"' . ' name="' . $inputName . '" value="<%- ' . $columnName . ' %>" '
                . ($column['size'] ? 'size="' . $column['size'] . '"' : '')
                . ' class="' . (isset($column['class']) ? $column['class'] : 'input-file') . '"'
                . (isset($column['style']) ? ' style="' . $column['style'] . '"' : '')
                . '/><input type="hidden" id="' . $this->_getCellInputElementId('<%- _id %>', 'file_icon_hidden')
                . '"' . ' name="' . $hiddenInputName . '" value="<%- ' . $columnName . ' %>" '
                . ($column['size'] ? 'size="' . $column['size'] . '"' : '')
                . ' class="' . (isset($column['class']) ? $column['class'] : 'input-file') . '"'
                . (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
        }

        return '<input type="text" id="' . $this->_getCellInputElementId('<%- _id %>', $columnName) . '"'
            . ' name="' . $inputName
            . '" value="<%- ' . $columnName . ' %>" '
            . ($column['size'] ? 'size="' . $column['size'] . '"' : '')
            . ' class="' . (isset($column['class']) ? $column['class'] : 'input-text') . '"'
            . (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
