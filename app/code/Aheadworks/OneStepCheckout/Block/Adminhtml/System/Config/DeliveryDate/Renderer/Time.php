<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate\Renderer;

use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\Time as TimeSource;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class Time
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DeliveryDate\Renderer
 */
class Time extends Select
{
    /**
     * @var TimeSource
     */
    private $timeSource;

    /**
     * @param Context $context
     * @param TimeSource $timeSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        TimeSource $timeSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->timeSource = $timeSource;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->timeSource->toOptionArray());
        }
        $this->setClass('time-slot-select');
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
