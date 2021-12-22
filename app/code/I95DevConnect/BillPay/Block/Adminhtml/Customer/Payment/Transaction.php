<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Customer\Payment;

/**
 * Order history block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Transaction extends \Magento\Backend\Block\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;
    public $payments;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * set Payments
     * @param object
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * get payments
     */
    public function getPayments()
    {
        return $this->payments;
    }
}
