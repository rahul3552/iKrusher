<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\BillPay\Model\DataPersistence\CashReceipts\CashReceipts;

class Create
{
    const MAGLOGNAME = 'cashReceipt';
    const PENDING_STATUS = 'pending';
    const PAID_STATUS = 'paid';
    const PROCESSING_STATUS = 'processing';
    const COMPLETE_STATUS = 'complete';

    const ORDER_STATUS = 'order_status';
    const AMOUNT = 'appliedDocUnappliedAmount';
    const PRIMARY_ID = 'primary_id';
    const OUTSTANDING_AMOUNT = 'outstanding_amount';
    const ADOC_NUMBER = 'appliedDocumentNumber';
    const RECEIPT_DOC_NO = 'receiptDocumentNumber';
    const CUSTOMER_ENTITY = 'customerEntity';
    const ADAA = 'appliedDocAppliedAmount';
    const TARGET_CUSTOMER_ID = 'targetCustomerId';
    const PAYMENT_TYPE = 'paymentType';
    const MODIFIEDDATE = 'modifiedDate';
    const PAYMENT_C = 'paymentComments';
    const TRAN_ID = 'transactionId';
    const CUSTOMER = 'customer';
    const TARGET_INVOICE_ID = 'target_invoice_id';
    const DISCOUNTAMOUNT = 'discountAmount';

    public $logger;
    public $dataHelper;
    public $eventManager;
    public $validate;
    public $validateFields = [
        self::ADOC_NUMBER => 'i95dev_billpay_004'
    ];
    public $targetFieldErp = 'targetId';
    public $storeManager;
    public $stringData;
    public $entityCode;
    public $abstractDataPersistence;
    public $erpCode = "ERP";
    public $customerId = null;
    public $customer;

    public $customerType;
    public $entityType;
    public $accountRecv;

    /**
     * @var \I95DevConnect\BillPay\Model\ArPaymentFactory
     */
    protected $arPaymentModel;

    /**
     * @var \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory
     */
    protected $arPaymentDetailsModel;

    /**
     * @var \I95DevConnect\BillPay\Model\ArbookFactory
     */
    protected $arBookModel;

    /**
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \I95DevConnect\BillPay\Helper\Data
     */
    protected $billPayHelper;

    protected $pricingHelper;

    public $genericHelper;
    public $postData;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \I95DevConnect\BillPay\Helper\Data $billPayHelper
     * @param \I95DevConnect\BillPay\Model\ArPaymentFactory $arPaymentModel
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentDetailsModel
     * @param \I95DevConnect\BillPay\Model\ArbookFactory $arBookModel
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \I95DevConnect\BillPay\Helper\Data $billPayHelper,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $arPaymentModel,
        \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentDetailsModel,
        \I95DevConnect\BillPay\Model\ArbookFactory $arBookModel,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables\AccountReceivables\Info $accountRecv
    ) {
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->validate = $validate;
        $this->eventManager = $eventManager;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->storeManager = $storeManager;
        $this->billPayHelper = $billPayHelper;
        $this->arPaymentModel = $arPaymentModel;
        $this->arPaymentDetailsModel = $arPaymentDetailsModel;
        $this->arBookModel = $arBookModel;
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
        $this->pricingHelper = $pricingHelper;
        $this->genericHelper = $genericHelper;
        $this->accountRecv = $accountRecv;
    }

    public function createCR($stringData, $entityCode, $erp = null)
    {
        $this->stringData = $stringData;
        $this->entityCode = $entityCode;
        if ($erp) {
            $this->erpCode = $erp;
        }

        try {
            $this->validateData();
            $magentoId = 0;
            $detailFlag = false;
            $this->postData = [];
            $this->prepareData();
            if ($this->entityType === 'penalty' && $this->customerType === 'Company') {
                $this->stringData['targetCustomerId'] = $this->accountRecv->getTargetCustomerId($this->customerId);
            }
            else {
                $accountreceivables = "erpconnect_reverse_cashreciepts";
                $this->eventManager->dispatch($accountreceivables, ['currentObject' => $this]);
            }

            $type = $this->postData['type'];
            if ($type == 'cashreceipt') {
                $cashReceiptNumber = $this->postData[self::RECEIPT_DOC_NO];
                $arPayment = $this->arPaymentModel->create()->load($cashReceiptNumber, 'cash_receipt_number');
                $arPaymentData = $arPayment->getData();
                $this->logger->create()->createLog(
                    __METHOD__,
                    $arPaymentData,
                    self::MAGLOGNAME,
                    'info'
                );
                $magentoId = $this->processArPayment($arPayment,$arPaymentData,$detailFlag,$entityCode);

                $this->billPayHelper->updateCustomerCreditlimit($this->postData[self::CUSTOMER_ENTITY]);
            }
            if ($magentoId != 0) {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __("Cash Receipts synced successfully"),
                    $magentoId
                );
            } else {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                    "Unable to Sync Cash Receipts",
                    null
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                $ex->getMessage(),
                null
            );
        }
    }

    /**
     * Prepare data array
     */
    public function prepareData()
    {
        $this->postData['type'] = $this->dataHelper->getValueFromArray("appliedDocumentType", $this->stringData);
        $this->postData[self::RECEIPT_DOC_NO] = $this->dataHelper->getValueFromArray(
            "receiptDocumentNumber",
            $this->stringData
        );
        $this->postData[self::ADOC_NUMBER] = $this->dataHelper->getValueFromArray(
            "appliedDocumentNumber",
            $this->stringData
        );
        $this->postData[self::ADAA] = number_format((float)$this->dataHelper->getValueFromArray(
            "receiptAppliedAmount",
            $this->stringData
        ), 2, '.', '');
        $this->postData[self::AMOUNT] = number_format((float)$this->dataHelper->getValueFromArray(
            "appliedDocUnappliedAmount",
            $this->stringData
        ), 2, '.', '');
        $this->postData[self::TARGET_CUSTOMER_ID] = $this->dataHelper->getValueFromArray(
            self::TARGET_CUSTOMER_ID,
            $this->stringData
        );
        $this->postData[self::PAYMENT_TYPE] = $this->dataHelper->getValueFromArray("paymentType", $this->stringData);
        $this->postData[self::MODIFIEDDATE] = $this->dataHelper->getValueFromArray("modifiedDate", $this->stringData);
        $this->postData[self::PAYMENT_C] = $this->dataHelper->getValueFromArray("paymentComment", $this->stringData);
        $this->postData[self::TRAN_ID] = $this->dataHelper->getValueFromArray("transactionId", $this->stringData);
        $this->postData[self::DISCOUNTAMOUNT] = number_format((float)$this->dataHelper->getValueFromArray(
            "discountAmount",
            $this->stringData
        ), 2, '.', '');

        $this->postData['appliedDocFinalAmount'] = number_format(($this->postData[self::ADAA] - $this->postData[self::DISCOUNTAMOUNT]), 2);
        $this->postData[self::CUSTOMER_ENTITY][self::TARGET_CUSTOMER_ID] = $this->dataHelper->getValueFromArray(
            self::TARGET_CUSTOMER_ID,
            $this->stringData[self::CUSTOMER]
        );
        $this->postData[self::CUSTOMER_ENTITY]['creditLimitType'] = $this->dataHelper->getValueFromArray(
            "creditLimitType",
            $this->stringData[self::CUSTOMER]
        );
        $this->postData[self::CUSTOMER_ENTITY]['creditLimitAmount'] = $this->dataHelper->getValueFromArray(
            "creditLimitAmount",
            $this->stringData[self::CUSTOMER]
        );
        $this->postData[self::CUSTOMER_ENTITY]['availableLimit'] = $this->dataHelper->getValueFromArray(
            "availableLimit",
            $this->stringData[self::CUSTOMER]
        );
        $this->postData[self::CUSTOMER_ENTITY]['sourceId'] = $this->customerId;
    }

    public function getArPaymentDetailsCollection($arPaymentData)
    {
        $collectionDetails = $this->arPaymentDetailsModel->create()
            ->getCollection()
            ->addFieldToFilter('payment_id', $arPaymentData[self::PRIMARY_ID])
            ->addFieldToFilter(self::TARGET_INVOICE_ID, $this->postData[self::ADOC_NUMBER]);
        return $collectionDetails->getData();
    }

    public function updateArPaymentDetails($arPaymentDetailsData, $arPayment)
    {
        foreach ($arPaymentDetailsData as $eachDetail) {
            if ($eachDetail[self::TARGET_INVOICE_ID]
                == $this->postData[self::ADOC_NUMBER]) {
                $arCollection = $this->arBookModel->create()->load($eachDetail['ar_id']);
                $arCollection->setOrderStatus(self::COMPLETE_STATUS);
                $arCollection->setData(
                    self::OUTSTANDING_AMOUNT,
                    $this->postData[self::AMOUNT]
                );
                $arCollection->save();
            }
        }
        $arPayment->setStatus(self::COMPLETE_STATUS);
        $arPayment->save();
        $this->sendEmailToCustomer($arPayment);
    }

    public function createArPaymentDetails($arPayment)
    {
        $arCollection = $this->arBookModel->create()
            ->load($this->postData[self::ADOC_NUMBER], self::TARGET_INVOICE_ID);
        if ($arCollection->getPrimaryId()) {
            $arCollection->setOrderStatus(self::COMPLETE_STATUS);
            $arCollection->setData(
                self::OUTSTANDING_AMOUNT,
                $this->postData[self::AMOUNT]
            );
        }
        $arCollection->save();
        $arpaymentDetails = $this->arPaymentDetailsModel->create();
        $arpaymentDetails->setPaymentId($arPayment->getData(self::PRIMARY_ID));
        $arpaymentDetails->setTargetInvoiceId($this->postData[self::ADOC_NUMBER]);
        $arpaymentDetails->setAmount($this->postData[self::ADAA]);
        $arpaymentDetails->setStatus(self::PAID_STATUS);
        $arpaymentDetails->setArId($arCollection->getPrimaryId());
        $arpaymentDetails->save();
        $arPayment->setStatus(self::COMPLETE_STATUS);
        $arPayment->save();
        $this->sendEmailToCustomer($arPayment);
    }

    public function createArPayment($customerID)
    {
        $paymentType = $this->getPaymentType();
        $arPayment = $this->arPaymentModel->create();
        $arPayment->setPaymentType($paymentType);
        $arPayment->setPaymentDate($this->postData[self::MODIFIEDDATE]);
        $arPayment->setPaymentTransId($this->postData[self::TRAN_ID]);
        $arPayment->setStatus(self::COMPLETE_STATUS);
        $arPayment->setCustomerId($customerID);
        $arPayment->setTotalAmt($this->postData['appliedDocFinalAmount']);
        $arPayment->setTargetSyncStatus(1);
        $arPayment->setCashReceiptNumber(
            $this->postData[self::RECEIPT_DOC_NO]
        );
        $arPayment->setPaymentComment($this->postData[self::PAYMENT_C]);
        $arPayment->save();

        return $arPayment;
    }

    public function setArBookStatus()
    {
        if (isset($this->postData[self::ADOC_NUMBER]) &&
                ($this->postData[self::AMOUNT] != '0' ||
                $this->postData[self::AMOUNT] != '0.00000')) {
            $arData = $this->getArBookCollection();
            $size = count($arData);
            $primaryId = $arData[$size - 1][self::PRIMARY_ID];
            $arModel = $this->arBookModel->create()->load($primaryId);
            /* set status on ar book table */
            $arModel->setData(self::ORDER_STATUS, self::PENDING_STATUS);
            $arModel->setData(
                self::OUTSTANDING_AMOUNT,
                $this->postData[self::AMOUNT]
            );
            $arModel->save();
        }
    }

    public function getArBookCollection()
    {
        $filterARCollection = $this->arBookModel->create()->getCollection()
        ->addFieldToFilter(
            self::TARGET_INVOICE_ID,
            $this->postData[self::ADOC_NUMBER]
        );
        return $filterARCollection->getData();
    }

    public function setArPaymentDetail($paymentId, $primaryId)
    {
        $arPaymentsDetails = $this->arPaymentDetailsModel->create()->load($paymentId, "payment_id");
        $arPaymentsDetails->setTargetInvoiceId(
            $this->postData[self::ADOC_NUMBER]
        );
        $arPaymentsDetails->setPaymentId($paymentId);
        $arPaymentsDetails->setStatus(self::PAID_STATUS);
        $arPaymentsDetails->setAmount($this->postData[self::ADAA]);
        $arPaymentsDetails->setArId($primaryId);
        $arPaymentsDetails->save();
    }

    public function getPaymentType()
    {
        $paymentType = '';
        switch ($this->postData[self::PAYMENT_TYPE]) {
            case 'cash':
                $paymentType = 'cashondelivery';
                break;
            case 'cashondelivery':
                $paymentType = $this->postData[self::PAYMENT_TYPE];
                break;
            case 'checkmo':
                $paymentType = $this->postData[self::PAYMENT_TYPE];
                break;
            case 'authnetcim':
                $paymentType = $this->postData[self::PAYMENT_TYPE];
                break;
            case 'creditcard':
                $paymentType = 'authnetcim';
                break;
            default:
                $paymentType = '';
                break;
        }
        return $paymentType;
    }
    public function sendEmailToCustomer($payment)
    {
        try {
            $paymentId = $payment->getData(self::PRIMARY_ID);
            $paymentTransId = $payment->getData('payment_trans_id');
            $paymentStatus = $payment->getData('status');
            $paymentAmount = $payment->getData('total_amt');
            if ($this->customerId) {
                $paymentType = $this->getPaymentType();
                $mailParams = [
                    'CustomerId' => $this->customerId,
                    'ReceiptDocumentNumber' => $paymentId,
                    'PaymentType' => $paymentType,
                    'AppliedDocumentNumber' => $paymentTransId,
                    'PaymentStatus' => $paymentStatus,
                    'ModifiedDate' => $this->postData['modifiedDate'],
                    'PaymentComments' => $this->postData['paymentComments'],
                    'ReceiptDocumentAmount' => $this->formatPrice($paymentAmount),
                    'CashReceiptNumber' => $this->postData['receiptDocumentNumber']
                ];

                $this->billPayHelper->sendEmailToCustomer($mailParams);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );

            return false;
        }
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    /**
     * Validate Erp data. Check given customer exists or not. If exist initialize $this->customerId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData()
    {
        $isEnabled = $this->billPayHelper->isBillPayEnabled();
        if (!$isEnabled) {
            throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_billpay_001"));
        }
        $this->validate->validateFields = $this->validateFields;
        $this->validate->validateData($this->stringData);

        $companyadmin = "erpconnect_reverse_companyadmin";
        $this->eventManager->dispatch($companyadmin, ['currentObject' => $this]);

        $this->customerType = $this->dataHelper->getValueFromArray(
            "customerType",
            $this->stringData
        );

        $targetCustomerId = $this->dataHelper->getValueFromArray(
            "targetCustomerId",
            $this->stringData
        );
        $this->entityType = $this->dataHelper->getValueFromArray(
            "InitialDocumentType",
            $this->stringData
        );

        // @updatedBy Subhan. If type is penalty and customer, get it from string
        if ($this->entityType === 'penalty' && $this->customerType === 'Customer') {
            $customerData = $this->genericHelper->getCustomerInfoByTargetId($targetCustomerId);
        }
        else if ($this->entityType === 'penalty' && $this->customerType === 'Company') {
            $this->customerId = $this->dataHelper->getValueFromArray(
                "companyadminid",
                $this->stringData
            );
        }
        else {
            // @updatedBy Subhan. targetcustomerId is being retrieved from targetOrderId of input string
            $targetOrderId = $this->dataHelper->getValueFromArray(
                "targetOrderId",
                $this->stringData
            );

            $this->customer = $this->billPayHelper->getCustomerFromOrder($targetOrderId);
            $customerData = $this->genericHelper->getCustomerInfoByTargetId($this->customer[self::TARGET_CUSTOMER_ID]);
        }

        if (!empty($customerData) && isset($customerData[0])) {
            $this->customerId = $customerData[0]->getId();
        } else {
            if (!$this->customerId) {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_cust_012"));
            }
        }
    }

    public function setupArModel($arData,$entityCode)
    {
        $size = count($arData);
        $primaryId = $arData[$size - 1][self::PRIMARY_ID];
        $arModel = $this->arBookModel->create()->load($primaryId);
        $arModelData = $arModel->getData();
        $customerID = isset($arModelData['customer_id'])
            ? $arModelData['customer_id'] : '';
        $arPayment = $this->createArPayment($customerID);
        $paymentId = $arPayment->getData(self::PRIMARY_ID);

        /* set status on ar book table */
        $unappliedAmt = $this->postData[self::AMOUNT];
        if ($unappliedAmt == '0' || $unappliedAmt == '0.00000') {
            $arModel->setData(self::ORDER_STATUS, self::COMPLETE_STATUS);
            $arModel->setData(
                self::OUTSTANDING_AMOUNT,
                $this->postData[self::AMOUNT]
            );
        } else {
            $arModel->setData(self::ORDER_STATUS, self::PENDING_STATUS);
            $arModel->setData(
                self::OUTSTANDING_AMOUNT,
                $this->postData[self::AMOUNT]
            );
        }

        $beforeeventname = 'erpconnect_messagequeuetomagento_beforesave_' . $entityCode;
        $this->eventManager->dispatch($beforeeventname, ['arData' => $arModel, 'currentObject' => $this]);
        $arModel->save();

        $this->setArPaymentDetail($paymentId, $primaryId);
        $magentoId = $arPayment->getId();
        $this->sendEmailToCustomer($arPayment);

        return $magentoId;

    }

    public function processArPayment($arPayment, $arPaymentData, $detailFlag, $entityCode)
    {
        /* Check if payment already exist */
        if (!empty($arPaymentData)) {
            $arPaymentDetailsData = $this->getArPaymentDetailsCollection($arPaymentData);
            /* Check if paid without invoice */
            if (!(!empty($arPaymentDetailsData) && $detailFlag)) {
                $this->updateArPaymentDetails($arPaymentDetailsData, $arPayment);
            } else {
                $this->createArPaymentDetails($arPayment);
            }

            $this->setArBookStatus();
            $magentoId = $arPayment->getId();
        } else {
            $arData = $this->getArBookCollection();
            if (!empty($arData)) {
                $magentoId = $this->setupArModel($arData,$entityCode);
            } else {
            	throw new \Magento\Framework\Exception\LocalizedException(
            		__("Related AccountReveivables does not exists")
            	);
            }
        }
        return $magentoId;
    }
}
