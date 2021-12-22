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
class ReceiptDetails extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_BillPay::customer/receiptdetails.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $receipt;

    /**
     * @var CollectionFactoryInterface
     */
    private $receiptCollectionFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArbookFactory $receiptCollectionFactory
     * @param Session $customerSession
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArbookFactory $receiptCollectionFactory,
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
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->receipt) {
            $this->receipt = $this->receiptCollectionFactory->create();
            $this->receipt = $this->receipt->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            );
        }

        return $this->receipt;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getReceiptDetails()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'billpay.managepayment.pager'
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
