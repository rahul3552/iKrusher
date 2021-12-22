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
namespace Aheadworks\OneStepCheckout\Block\Order;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;

/**
 * Class DeliveryDate
 * @package Aheadworks\OneStepCheckout\Block\Order
 */
class DeliveryDate extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'order/info/delivery_date.phtml';

    /**
     * {@inheritdoc}
     */
    protected $_isScopePrivate = true;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

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
    private function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }
}
