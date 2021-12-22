<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_Smtp
 */


namespace Aitoc\Smtp\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class TestButton extends Field
{

    const TEMPLATE_PATH = 'Aitoc_Smtp::config/button.phtml';
    const CHECK_BUTTON_ID = 'aitoc_send_button';

    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Template $block */
        $block = $this->_layout->createBlock(\Magento\Backend\Block\Template::class);

        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData([
            'id' => self::CHECK_BUTTON_ID,
            'label' => __('Send Test Email'),
            'class' => 'primary'
        ]);

        $block
            ->setTemplate(self::TEMPLATE_PATH)
            ->setData('send_button', $button->toHtml())
            ->setData('ajax_url', $this->getAjaxUrl())
            ->setData('button_id', self::CHECK_BUTTON_ID);

        return $block->toHtml();
    }

    /**
     * Render scope label
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }

    /**
     * @return string
     */
    private function getAjaxUrl()
    {
        return $this->getUrl('aitoc_smtp/smtp/test', ['_current' => true]);
    }


    /**
     * @return string
     */
    public function getCheckButtonId()
    {
        return self::CHECK_BUTTON_ID;
    }
}
