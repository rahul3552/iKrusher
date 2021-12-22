<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Customer\Payment;

/**
 * List all payment details
 */
class PaymentList extends \Magento\Backend\Block\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $adminHelper;

    /**
     * @var \I95DevConnect\Billpay\Model\ArBookFactory
     */
    protected $paymentCollectionFactory;

    /**
     * @var
     */
    public $payments;
    public $arPaymentDetailsModel;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    public $pricingHelper;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    public $logger;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\Billpay\Model\ArBookFactory $paymentCollectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\BillPay\Model\Arbook $paymentCollectionFactory,
        \I95DevConnect\BillPay\Model\ArPaymentDetails $arPaymentDetailsModel,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->arPaymentDetailsModel = $arPaymentDetailsModel;
        $this->pricingHelper = $pricingHelper;
        $this->logger = $logger;

        parent::__construct($context, $data);
    }

    public function getPaymentsDetails()
    {
        $payment = $this->getPayments();
        if ($this->payments) {
            $paymentId = $payment['primary_id'];
            try {
                $paymentDetails = $this->arPaymentDetailsModel->getCollection()
                    ->addFieldToFilter('payment_id', $paymentId);
                $paymentDetailsData = $paymentDetails->getData();
                $paymentBookData = [];

                foreach ($paymentDetailsData as $payDetails) {
                    $invoiceId = $payDetails['target_invoice_id'];
                    $cpay = $this->paymentCollectionFactory->getCollection()
                        ->addFieldToFilter('target_invoice_id', $invoiceId)->getData();
                    $paymentBook = current($cpay);
                    $paymentBook['amount'] = $payDetails['amount'];
                    $paymentBook['status'] = $payDetails['status'];
                    $paymentBookData[] = $paymentBook;
                }
            } catch (\Exception $ex) {
                $this->logger->error(__METHOD__, $ex->getMessage());
            }

            return $paymentBookData;
        }
    }

    /**
     * set payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * get Payments
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
    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    /**
     * get amount adjusted
     * @param int $paymentId
     * @return int
     */
    public function getAmountAdjusted($paymentId)
    {
        $payment = $this->arPaymentDetailsModel->getCollection()
            ->addFieldToFilter('payment_id', $paymentId)->getFirstItem();

        return $payment->getAmount();
    }
}
