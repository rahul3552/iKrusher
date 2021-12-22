<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\CloudConnect\Block\System\Config\Payment;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url;

/**
 * Payment mapping button class
 */
class Button extends Field
{
    protected $_template = 'I95DevConnect_CloudConnect::system/config/payment/button.phtml';

    /**
     * @var Url
     */
    public $urlHelper;

    /**
     * Button constructor.
     * @param Context $context
     * @param Url $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $urlHelper,
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
        return $this->urlHelper->getUrl('cloudconnect/payment/index');
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        try {
            
            $button = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Button::class
            )->setData(
                [
                    'id' => 'sync_payment_method',
                    'label' => __('Click here to Sync Now'),
                ]
            );
            
        } catch (LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
        return $button->toHtml();
    }
}
