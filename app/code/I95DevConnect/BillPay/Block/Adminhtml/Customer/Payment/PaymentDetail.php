<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Customer\Payment;

/**
 * Payment Detail container block
 */
class PaymentDetail extends \Magento\Backend\Block\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public $payments;

    public $scopeConfig;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    public $pricingHelper;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->scopeConfig = $context->getScopeConfig();
        $this->pricingHelper = $pricingHelper;

        parent::__construct($context, $data);
    }

    /**
     * set payment
     * @param object $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * get payment
     *
     * @return object payment
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * format price
     * @param int $price
     * @return string
     */
    public function formatPrice($price, $paymentId = null)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    /**
     * Get Payment Type title
     *
     * @param string $payment_type
     * @return string
     */
    public function getPaymentTypeTitle($payment_type)
    {
        return $this->scopeConfig->getValue('payment/' . $payment_type . '/title');
    }
}
