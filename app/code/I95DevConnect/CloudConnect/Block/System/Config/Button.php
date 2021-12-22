<?php

namespace I95DevConnect\CloudConnect\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class for adding a button for shipping mapping
 */
class Button extends Field
{
    protected $_template = 'I95DevConnect_CloudConnect::system/config/button.phtml';

    /**
     * Button constructor.
     * @param Context $context
     * @param \Magento\Framework\Url $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Url $urlHelper,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;

        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string|null
     */
    public function getButtonUrl()
    {
        return $this->urlHelper->getUrl('cloudconnect/get/index');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'sync_shipmethod',
                'label' => __('Click here to Sync Now'),
            ]
        );
        return $button->toHtml();
    }
}
