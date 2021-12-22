<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Index;

use \Magento\Customer\Model\Session;

/**
 * Sales order history block
 */
class Outstandingdetail extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var CollectionFactoryInterface
     */
    private $receiptCollectionFactory;
    protected $receipt;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $receiptCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $receiptCollectionFactory,
        Session $customerSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        $this->receiptCollectionFactory = $receiptCollectionFactory;
        $this->customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getReceiptDetails()
    {
        if (!($this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->receipt) {
            $this->receipt = $this->receiptCollectionFactory->create()->getCollection()->addFieldToFilter(
                'target_invoice_id',
                $this->getRequest()->getParam('invoice_id')
            );

            $this->receipt->getSelect()->joinLeft(
                ['i95dev_ar_payment' => $this->receipt->getTable('i95dev_ar_payment')],
                'main_table.payment_id = i95dev_ar_payment.primary_id',
                ['i95dev_ar_payment.payment_type',
                'i95dev_ar_payment.payment_date',
                'i95dev_ar_payment.cash_receipt_number']
            );
            $this->receipt->addFieldToFilter('main_table.status', 'paid');
        }

        return $this->receipt;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getReceiptDetails() && !$this->getLayout()->getBlock('billpay.outstandingdetails.pager')) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'billpay.outstandingdetails.pager'
            )->setCollection(
                $this->getReceiptDetails()
            );

            $this->setChild('pager', $pager);

            $this->getReceiptDetails()->load();
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
        return $this->pricingHelper->currency($price, true, false);
    }
}
