<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Customer\PaymentDetails;

use \Magento\Customer\Model\Session;

/**
 * BillPay payment details
 */
class PaymentDetails extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_BillPay::customer/paymentdetail/payment.phtml';
    protected $payments;

    /**
     * @var \I95DevConnect\Billpay\Model\ArPaymentDetailsFactory
     */
    private $paymentCollectionFactory;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $paymentCollectionFactory
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $paymentCollectionFactory,
        Session $customerSession,
        array $data = []
    ) {
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->_customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    /**
     * set payment
     * @return object
     */
    public function setPayment($payment)
    {
        $this->payments = $payment;
    }

    /**
     * get payment
     * @return object
     */
    public function getPayment()
    {
        return $this->payments;
    }
}
