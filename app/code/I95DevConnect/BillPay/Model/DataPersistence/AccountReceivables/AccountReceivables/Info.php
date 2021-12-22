<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables\AccountReceivables;

/**
 * Model for definition of API Methods
 */
class Info
{

    const EMAIL = 'email';
    const PAYMENT_TRANS_ID = 'payment_trans_id';

    /**
     * Custom logs
     */
    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';
    const PENDING_STATUS = 'pending';

    public $dataHelper;
    public $eventManager;
    public $fieldMapInfo = [
        'sourceId' => 'id',
        self::EMAIL => self::EMAIL,
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'reference' => self::EMAIL,
    ];

    /**
     *
     * @var \I95DevConnect\BillPay\Model\ArPaymentFactory
     */
    protected $arPayment;

    /**
     *
     * @var \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory
     */
    protected $arPaymentDetails;

    /**
     *
     * @var \I95DevConnect\BillPay\Model\ArbookFactory
     */
    protected $arBook;

    public $customerData;

    public $genericHelper;

    /**
     *
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentDetails
     * @param \I95DevConnect\BillPay\Model\ArbookFactory $arBook
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment,
        \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentDetails,
        \I95DevConnect\BillPay\Model\ArbookFactory $arBook,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->eventManager = $eventManager;
        $this->arPayment = $arPayment;
        $this->arPaymentDetails = $arPaymentDetails;
        $this->arBook = $arBook;
        $this->genericHelper = $genericHelper;
    }

    public function getInfo($paymentId)
    {
        $this->paymentId = $paymentId;
        $this->InfoData = $this->getArPayment($paymentId);
        $this->customerData = $this->genericHelper->getCustomerInfoByTargetId($this->InfoData['TargetCustomerId']);
        $arpaymentInfoEvent = "erpconnect_forward_arpaymentinfo";
        $this->eventManager->dispatch($arpaymentInfoEvent, ['currentObject' => $this]);
        return $this->InfoData;
    }

    public function getArPayment($paymentId)
    {
        $arPaymentReturnData = [];
        $arPaymentData = $this->arPayment->create()->load($paymentId);
        $customerId = $arPaymentData->getCustomerId();
        $arPaymentReturnData['TargetCustomerId'] = $this->getTargetCustomerId($customerId);
        $arPaymentReturnData['sourceId'] = $paymentId;
        $arPaymentReturnData['paymentRefNo'] = $arPaymentData->getData(self::PAYMENT_TRANS_ID);
        $arPaymentReturnData['paymentComment'] = $arPaymentData->getData('payment_comment');
        $arPaymentReturnData['documentAmount'] = $arPaymentData->getData('total_amt');
        $arPaymentReturnData['paymentDate'] = $arPaymentData->getData('payment_date');
        $arPaymentReturnData['cashReceiptNumber'] = $arPaymentData->getData('cash_receipt_number');
        $arPaymentReturnData['transactionNumber'] = $arPaymentData->getData(self::PAYMENT_TRANS_ID);
        $arPaymentReturnData['notes'] = "";
        $arPaymentReturnData['userEntered'] = 'admin';
        $arPaymentReturnData['payment'] = $this->getPaymentEntity($arPaymentData);
        $arEntities = $this->getArEntities($arPaymentData);
        $notes = implode(',', $arEntities['erpInvoiceIds']);
        $arPaymentReturnData['notes'] = $notes;
        $arPaymentReturnData['aRPostedInvoice'] = $arEntities['invoiceEntityList'];
        $arPaymentReturnData['aRReturns'] = $arEntities['returnEntityList'];

        return $arPaymentReturnData;
    }

    public function getTargetCustomerId($customerId)
    {
        $customer = $this->genericHelper->getCustomerById($customerId);
        $targetCustomerId = "";
        if (isset($customer['custom_attributes'])) {
            foreach ($customer['custom_attributes'] as $customAttribute) {
                if ($customAttribute['attribute_code'] == "target_customer_id") {
                    $targetCustomerId = $customAttribute['value'];
                }
            }
        }
        return $targetCustomerId;
    }

    /**
     * Formation of Payment Entity
     * @Param Array $paymentData
     * @return object $paymentTransEntityArr
     */
    public function getPaymentEntity($paymentData)
    {
        $paymentTransEntity = [];
        $paymentType = $paymentData->getData('payment_type');
        $paymentTransEntity['paymentName'] = $paymentType;
        if ($paymentType != "checkmo" && $paymentType != "cashondelivery") {
            $cardType = 'VI';
            $ccLast = '1111';
            $ccExpMonth = date('m');
            $ccExpYear = date('Y', strtotime('+1 year'));
            $paymentTransEntity['creditCardNumber'] = $ccLast;
            $paymentTransEntity['cvv'] = $ccLast;
            $paymentTransEntity['expiryMonth'] = $ccExpMonth;
            $paymentTransEntity['expiryYear'] = $ccExpYear;
            $paymentTransEntity['cardType'] = $cardType;
        }
        $paymentTransEntity['PaymentAmount'] = $paymentData->getData('total_amt');
        $paymentTransEntity['TransactionNumber'] = $paymentData->getData(self::PAYMENT_TRANS_ID);
        $paymentTransEntity['UserDefined'] = '';

        return $paymentTransEntity;
    }

    public function getArEntities($arPaymentData)
    {
        $returnEntityList = [];
        $invoiceEntityList = [];
        $erpInvoiceIds = [];

        $paymentId = $arPaymentData->getId();
        $paymentDate = $arPaymentData->getData('payment_date');
        $paymentDetailsData = $this->arPaymentDetails->create()->getCollection()
            ->addFieldToFilter('payment_id', $paymentId);
        foreach ($paymentDetailsData as $paymentDetails) {
            $targetInvoiceId = $paymentDetails->getTargetInvoiceId();
            $arId = $paymentDetails->getArId();
            $amount = $paymentDetails->getAmount();

            $paymentBookData = $this->arBook->create()->getCollection()
                ->addFieldToFilter('primary_id', $arId)
                //->addFieldToFilter('order_status', 'processing')
                ->getData();
            foreach ($paymentBookData as $data) {
                if ($data['type'] == 'invoice' || $data['type'] == 'penalty') {
                    $disAmt = $this->getDiscountAmount($data, $amount);
                    $invoiceEntityList[] = $this->getARInvoiceEntity($data, $paymentDate, $amount, $disAmt);
                } else {
                    $returnEntityList[] = $this->getARReturnsEntity($data, $paymentDate, $amount);
                    $erpInvoiceIds[] = $targetInvoiceId . '-' . $amount;
                }
            }
        }

        return [
            "erpInvoiceIds" => $erpInvoiceIds,
            "invoiceEntityList" => $invoiceEntityList,
            "returnEntityList" => $returnEntityList
        ];
    }

    /**
     * Formation of Invoice Entity
     * @Param Array $invoiceData
     *         Date $paymentDate
     *         Float $amount
     * @return object $invoiceEntity
     */
    public function getARInvoiceEntity($invoiceData, $paymentDate, $amount, $disAmt)
    {
        $invoiceEntity = [];
        $invoiceEntity['receiptDocumentNumber'] = '';
        $invoiceEntity['receiptAppliedAmount'] = $amount;
        $invoiceEntity['invoiceDate'] = $paymentDate;
        $invoiceEntity['sourceInvoiceId'] = '';
        $invoiceEntity['appliedDocumentNumber'] = $invoiceData['target_invoice_id'];
        $invoiceEntity['targetOrderId'] = $invoiceData['target_order_id'];
        $invoiceEntity['appliedDocTotalAmount'] = $invoiceData['invoice_amount'];
        $invoiceEntity['appliedDocumentType'] = $invoiceData['type'];
        $invoiceEntity['discountAmount'] = $disAmt;
        $invoiceEntity['penaltyAmount'] = '';

        return $invoiceEntity;
    }

    /**
     * Formation of Return Entity
     * @Param Array $returnData
     *         Date $paymentDate
     *         Float $amount
     * @return object $returnEntity
     */
    public function getARReturnsEntity($returnData, $paymentDate, $amount)
    {
        $returnEntity = [];
        $returnEntity['receiptDocumentNumber'] = "";
        $returnEntity['appliedDocTotalAmount'] = $returnData['outstanding_amount'];
        $returnEntity['returnDocumentDate'] = $paymentDate;
        $returnEntity['sourceInvoiceId'] = "";
        $returnEntity['appliedDocumentNumber'] = $returnData['target_invoice_id'];
        $returnEntity['targetOrderId'] = $returnData['target_order_id'];
        $returnEntity['appliedDocAppliedAmount'] = $returnData['invoice_amount'];
        $returnEntity['returnAdjustmentAmount'] = $amount;

        return $returnEntity;
    }

    public function getDiscountAmount($data, $amount)
    {
        $discountDate=strtotime($data['discount_dt']);
        $discountAmount=$data['discount_amount'];
        $outStandingAmount=$data['outstanding_amount'];
        $currentDate = strtotime(date('Y-m-d'));
        $totalAmount=$discountAmount+$amount;
        $disAmt=0.000;

        if ($currentDate < $discountDate) {
            if ((float)$outStandingAmount == (float)$totalAmount) {
                $disAmt= $discountAmount;
            } else {
                $disAmt= 0.000;
            }
        }
        return $disAmt;
    }
}
