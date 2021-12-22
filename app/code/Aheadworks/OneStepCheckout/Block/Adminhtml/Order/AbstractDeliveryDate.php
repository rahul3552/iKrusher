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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Sales\Model\Order;

/**
 * Class AbstractDeliveryDate
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Order
 */
abstract class AbstractDeliveryDate extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'order/delivery_date.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->getDeliveryDate()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Get delivery date
     *
     * @return string
     */
    public function getDeliveryDate()
    {
        return $this->getOrder()->getAwDeliveryDate();
    }

    /**
     * Get delivery from date
     *
     * @return string
     */
    public function getDeliveryDateFrom()
    {
        return $this->getOrder()->getAwDeliveryDateFrom();
    }

    /**
     * Get delivery to date
     *
     * @return string
     */
    public function getDeliveryDateTo()
    {
        return $this->getOrder()->getAwDeliveryDateTo();
    }

    /**
     * Get current order
     *
     * @return Order
     */
    abstract protected function getOrder();
}
