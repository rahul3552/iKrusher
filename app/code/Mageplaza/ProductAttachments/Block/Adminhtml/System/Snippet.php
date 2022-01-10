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

namespace Mageplaza\ProductAttachments\Block\Adminhtml\System;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Snippet
 * @package Mageplaza\ProductAttachments\Block\Adminhtml\System
 */
class Snippet extends Field
{
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
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div class="control-value" style="padding-top: 8px">';

        $html .= '<strong>' . __('Static Block') . '</strong><br />';
        $html .= '<pre style="background-color: #f5f5dc">'
            . '<code>{{block class="Mageplaza\ProductAttachments\Block\Product\Display\Inline"}}</code>'
            . '</pre>';

        $html .= '<strong>' . __('.phtml file') . '</strong><br />';
        $html .= '<pre style="background-color: #f5f5dc"><code>' . $this->_escaper->escapeHtml('<?=
 $block->getLayout()
            ->createBlock("Mageplaza\ProductAttachments\Block\Product\Display\Inline")
            ->toHtml();
?>') . '</code></pre>';

        $html .= '<strong>' . __('Layout file') . '</strong><br />';
        $html .= '<pre style="background-color: #f5f5dc"><code>' . $this->_escaper->escapeHtml(
            '<block class="Mageplaza\ProductAttachments\Block\Product\Display\Inline"'
                . ' name="mpattachments.inline.sub" />'
        ) . '</code></pre>';

        $html .= '</div>';

        return $html;
    }
}
