<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Customer\PaymentDetails;

use \Magento\Customer\Model\Session;

/**
 * Sales order history block
 */
class TransactionDetails extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_BillPay::customer/paymentdetail/transaction.phtml';

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $payments;

    /**
     * @var CollectionFactoryInterface
     */
    private $paymentCollectionFactory;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $paymentCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Request\Http $request
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
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function setPayment($payment)
    {
        $this->payments = $payment;
    }

    public function getPayment()
    {
        return $this->payments;
    }
}
