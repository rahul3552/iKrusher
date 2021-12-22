<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Customer\PaymentDetails;

/**
 * List Payment details
 */
class ListPayment extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_BillPay::customer/paymentdetail/listpayment.phtml';
    protected $payments;
    public $priceHelper;

    /**
     * @var \I95DevConnect\Billpay\Model\ArBookFactory
     */
    private $paymentCollectionFactory;
    protected $arPaymentDetailsModel;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\Arbook $paymentCollectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetails $arPaymentDetailsModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\Arbook $paymentCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \I95DevConnect\BillPay\Model\ArPaymentDetails $arPaymentDetailsModel,
        array $data = []
    ) {
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->priceHelper = $priceHelper;
        $this->arPaymentDetailsModel = $arPaymentDetailsModel;

        parent::__construct($context, $data);
    }

    public function getPaymentsDetails()
    {
        $payment = $this->getPayment();
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
                $this->_logger->error(__METHOD__, $ex->getMessage());
            }

            return $paymentBookData;
        }
    }

    /**
     * set payments
     */
    public function setPayment($payment)
    {
        $this->payments = $payment;
    }

    /**
     *  get payments object
     */
    public function getPayment()
    {
        return $this->payments;
    }

    /**
     *  format price
     */
    public function formatPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
