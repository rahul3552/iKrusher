<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Customer;

use \Magento\Customer\Model\Session;

/**
 * Sales order history block
 */
class MyPayments extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_BillPay::customer/mypayments.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $payments;

    /**
     * @var CollectionFactoryInterface
     */
    private $paymentCollectionFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $paymentCollectionFactory,
        Session $customerSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getPayments()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->payments) {
            $this->payments = $this->paymentCollectionFactory->create();
            $this->payments = $this->payments->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            );
        }

        return $this->payments;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPayments()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'billpay.managepayment.pager'
            )->setCollection(
                $this->getPayments()
            );

            $this->setChild('pager', $pager);
            $this->getPayments()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency((float)$price, true, false);
    }

    /**
     * @param object $payment
     * @return string
     */
    public function getViewUrl($payment)
    {
        return $this->getUrl('billpay/index/paymentdetails', ['payment_id' => $payment->getPrimaryId()]);
    }
}
