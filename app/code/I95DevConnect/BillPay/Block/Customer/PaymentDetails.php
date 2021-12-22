<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Customer;

use \Magento\Customer\Model\Session;
use \Magento\Framework\App\Request\Http;

/**
 * Sales order history block
 */
class PaymentDetails extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_BillPay::customer/paymentdetails.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $payments;
    protected $request;

    /**
     * @var CollectionFactoryInterface
     */
    private $paymentCollectionFactory;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArPaymentFactory $paymentCollectionFactory
     * @param Session $customerSession
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $paymentCollectionFactory,
        Session $customerSession,
        Http $request,
        array $data = []
    ) {
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->customerSession = $customerSession;
        $this->request = $request;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Payment Information'));

        return $this;
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getPaymentsDetails()
    {
        if (!($this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->payments) {
            $this->payments = $this->paymentCollectionFactory->create();
            $this->payments = $this->payments->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'primary_id',
                $this->request->getParam('payment_id')
            )->getFirstItem();

            return $this->payments->getData();
        }
    }
}
