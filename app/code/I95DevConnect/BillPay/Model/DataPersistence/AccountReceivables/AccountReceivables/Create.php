<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables\AccountReceivables;

class Create
{
    const RETURNEXCEPTION = 'returnException';
    const RETURNINFO = 'returnInfo';
    const PENDING_STATUS = 'pending';
    const PAID_STATUS = 'paid';
    const PROCESSING_STATUS = 'processing';
    const COMPLETE_STATUS = 'complete';

    public $logger;
    public $dataHelper;
    public $eventManager;
    public $validate;
    public $validateFields = [
        'targetCustomerId' => 'i95dev_cust_003',
        'outstandingAmount' => 'i95dev_billpay_002',
        'reference' => 'i95dev_billpay_003'
    ];
    public $targetFieldErp = 'targetId';
    public $storeManager;
    public $stringData;
    public $entityCode;
    public $abstractDataPersistence;
    public $customerId;
    public $customerDetails = [];
    public $customer;
    /**
     * @var array
     */
    public $postData;

    /**
     * @var \I95DevConnect\BillPay\Helper\Data
     */
    protected $billPayHelper;

    /**
     * @var \I95DevConnect\BillPay\Model\ArPaymentFactory
     */
    protected $arPayment;

    /**
     * @var \I95DevConnect\BillPay\Model\ArbookFactory
     */
    protected $arBookModel;

    /**
     * @var \I95DevConnect\MessageQueue\Model\SalesOrderFactory
     */
    protected $i95devOrderFactory;

    /**
     * @var \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory
     */
    protected $i95devInvoiceFactory;

    /**
     * @var \I95DevConnect\BillPay\Model\ArPenaltyFactory
     */
    protected $arPenaltyModel;

    /**
     * @var \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory
     */
    protected $arPenaltyDetailsModel;

    /**
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    public $erpCode = "ERP";

    public $genericHelper;

    /**
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \I95DevConnect\BillPay\Helper\Data $billPayHelper
     * @param \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment
     * @param \I95DevConnect\BillPay\Model\ArbookFactory $arBookModel
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $i95devOrderFactory
     * @param \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $i95devInvoiceFactory
     * @param \I95DevConnect\BillPay\Model\ArPenaltyFactory $arPenaltyModel
     * @param \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory $arPenaltyDetailsModel
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     */
    public function __construct( // NOSONAR
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \I95DevConnect\BillPay\Helper\Data $billPayHelper,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment,
        \I95DevConnect\BillPay\Model\ArbookFactory $arBookModel,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $i95devOrderFactory,
        \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $i95devInvoiceFactory,
        \I95DevConnect\BillPay\Model\ArPenaltyFactory $arPenaltyModel,
        \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory $arPenaltyDetailsModel,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->validate = $validate;
        $this->eventManager = $eventManager;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->storeManager = $storeManager;
        $this->billPayHelper = $billPayHelper;
        $this->arPayment = $arPayment;
        $this->arBookModel = $arBookModel;
        $this->i95devOrderFactory = $i95devOrderFactory;
        $this->i95devInvoiceFactory = $i95devInvoiceFactory;
        $this->arPenaltyModel = $arPenaltyModel;
        $this->arPenaltyDetailsModel = $arPenaltyDetailsModel;
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
        $this->genericHelper = $genericHelper;
    }

    public function create($stringData, $entityCode, $erp = null) // NOSONAR
    {
        $this->stringData = $stringData;
        $this->entityCode = $entityCode;
        if ($erp) {
            $this->erpCode = $erp;
        }
        try {
            $this->validateData();
            $this->postData = [];
            $magentoId = 0;
            $this->prepareData();
            $type = $this->postData['type'];
            if ($type == 'cashreceipt') {
                $result = $this->updateCashReceipts();
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __("Account Recievables synced successfully"),
                    $result
                );
            } else {
                $reference = $this->postData['reference'];
                if ($reference) {
                    $arBookData = $this->arBookModel->create()->getCollection()
                        ->addFieldtoFilter('target_invoice_id', $reference)
                        ->getData();
                    $outstandingAmt = $this->postData['outstandingAmount'];
                    if (empty($arBookData)) {
                        $arBookData = $this->setARbookData(
                            $this->customerId,
                            $type
                        );
                    } else {
                        $arBookDataId = $arBookData[0]['primary_id'];
                        $invoiceAmount = $this->postData['invoiceAmount'];
                        $arBookData = $this->arBookModel->create()->load($arBookDataId);
                        if ($outstandingAmt == '0' || $outstandingAmt == '0.00000') {
                            $arBookData->setData('order_status', self::COMPLETE_STATUS);
                            $arBookData->setData('invoice_amount', $invoiceAmount);
                            $arBookData->setData('outstanding_amount', $outstandingAmt);
                            $arBookData->setData(
                                'modified_date',
                                $this->postData['invoiceDate']
                            );
                        } else {
                            $invoiceStatus = isset($arBookData['order_status'])
                                ? $arBookData['order_status'] : '';
                            if ($invoiceStatus == self::PROCESSING_STATUS) {
                                $arBookData = $this->setARbookData(
                                    $this->customerId,
                                    $type
                                );
                            }
                            if ($invoiceStatus == self::PENDING_STATUS) {
                                $arBookData->setData('invoice_amount', $invoiceAmount);
                                $arBookData->setData('outstanding_amount', $outstandingAmt);
                                $arBookData->setData(
                                    'modified_date',
                                    $this->postData['modifiedDate']
                                );
                            }
                        }
                    }

                    $discountAmount = $this->postData['discountAmount'];
                    if ($discountAmount == 0) {
                        $discountAmount = "0.00";
                    }
                    $arBookData->setData('discount_amount', $discountAmount);
                    $arBookData->setData('discount_dt', $this->postData['discountDate']);
                    $arBookData->setData('interest_amount', "0.00");
                    $beforeeventname = 'erpconnect_messagequeuetomagento_beforesave_' . $entityCode;
                    $this->eventManager->dispatch($beforeeventname, ['arData' => $arBookData, 'currentObject' => $this]);
                    $arBookData->Save();
                    $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_'.$entityCode;
                    $this->eventManager->dispatch($aftereventname, ['arData' => $arBookData, 'currentObject' => $this]);
                    $magentoId = $arBookData->getId();
                    if ($type == "penalty") {
                        $this->setARPenalty($arBookData);
                        $this->billPayHelper->updateCustomerCreditlimit($this->postData['customerEntity']);
                    }
                } else {
                    return $this->abstractDataPersistence->setResponse(
                        \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                        "Required Invoice Target ID",
                        null
                    );
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                $ex->getMessage(),
                null
            );
        }

        if ($magentoId != 0) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                __("Account Recievables synced successfully"),
                $magentoId
            );
        } else {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                "Unable to Sync Account Recievables",
                null
            );
        }
    }

    /**
     * Validate details provided by ERP.
     *
     * @return boolean
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

        $customerType = $this->dataHelper->getValueFromArray(
            "customerType",
            $this->stringData
        );

        $companyadmin = "erpconnect_reverse_companyadmin";
        $this->eventManager->dispatch($companyadmin, ['currentObject' => $this]);

        $targetCustomerId = $this->dataHelper->getValueFromArray(
            "targetCustomerId",
            $this->stringData
        );
        $entityType = $this->dataHelper->getValueFromArray(
            "type",
            $this->stringData
        );

        // @updatedBy Subhan. If type is penalty and customer, get it from string
        if ($entityType === 'penalty' && $customerType === 'Customer') {
            $customerData = $this->genericHelper->getCustomerInfoByTargetId($targetCustomerId);
        }
        else if ($entityType === 'penalty' && $customerType === 'Company') {
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
            $customerData = $this->genericHelper->getCustomerInfoByTargetId($this->customer['targetCustomerId']);
        }

        if (!empty($customerData) && isset($customerData[0])) {
            $this->customerId = $customerData[0]->getId();
        } else {
            if (!$this->customerId) {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_cust_012"));
            }
        }

        if ($this->postData['type'] == 'invoice') {
            $invoiceData = $this->i95devInvoiceFactory->create()
                ->load($this->postData['reference'], 'target_invoice_id');
            if (!$invoiceData->getSourceInvoiceId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__("Invoice does not exists"));
            }
        }
    }

    /**
     * Creating a new AR Record
     * @Params int $customerID
     * float $outstandingAmt
     * object $_eachArrBillPay
     * @return boolean
     */
    public function setARbookData($customerID, $type)
    {
        $arModel = $this->arBookModel->create();
        if ($type == 'penalty') {
            $targetOrderId = '';
            $poNumber = '';
        } else {
            $targetOrderId = $this->postData['targetOrderId'];
            $poNumber = $this->postData['poNumber'];
            $magentoOrderId = $this->getMagentoOrderId($targetOrderId);
            $arModel->setData('target_order_id', $targetOrderId);
            $arModel->setData('magento_order_id', $magentoOrderId);
        }

        $arModel->setData('customer_id', $customerID);
        $arModel->setData('target_customer_id', $this->postData['targetCustomerId']);
        $arModel->setData('target_invoice_id', $this->postData['reference']);
        $arModel->setData('invoice_amount', $this->postData['invoiceAmount']);
        $arModel->setData('outstanding_amount', $this->postData['outstandingAmount']);
        $arModel->setData('customer_po_number', $poNumber);
        $arModel->setData('modified_date', $this->postData['invoiceDate']);
        $arModel->setData('due_date', $this->postData['dueDate']);
        $arModel->setData('type', $type);
        $arModel->setData('order_status', self::PENDING_STATUS);
        return $arModel;
    }

    public function setARPenalty($arBookData)
    {
        $arPenaltyCollection = $this->arPenaltyModel->create()->getCollection()
            ->addFieldToFilter("penalty_id", $this->postData['reference'])
            ->addFieldToFilter("ar_id", $arBookData->getId());
        $arPenalty = $arPenaltyCollection->getLastItem();
        $arPenaltyModel = $this->arPenaltyModel->create();
        if ($arPenalty->getId()) {
            $arPenaltyModel = $arPenaltyModel->load($arPenalty->getId());
        }
        $arPenaltyModel->setData('ar_id', $arBookData->getId());
        $arPenaltyModel->setData('penalty_id', $this->postData['reference']);
        $arPenaltyModel->setData('amount', $this->postData['outstandingAmount']);
        $arPenaltyModel->setData('term', $this->postData['penaltyTerm']);
        $arPenaltyModel->setData('additional_amount', $this->postData['additionalCharges']);
        $arPenaltyModel->setData('penalty_amount', $this->postData['penaltyCharges']);
        $arPenaltyModel->save();

        if (isset($this->postData['invoices']) && !empty($this->postData['invoices'])) {
            $this->deleteAdditionalCharges($arBookData);
            $this->setARPenaltyDetails($arBookData);
        }
    }

    public function setARPenaltyDetails($arBookData)
    {
        if (!isset($this->postData['invoices']) || empty($this->postData['invoices'])) {
            return null;
        }

        foreach ($this->postData['invoices'] as $referenceData) {
            $arPenaltyDetails = $this->arPenaltyDetailsModel->create();
            if ($referenceData["type"] != "additionalFee" && !empty($referenceData["targetInvoiceId"])) {
                $penalty = $this->arPenaltyDetailsModel->create()->getCollection()
                    ->addFieldToFilter("penalty_id", $this->postData['reference'])
                    ->addFieldToFilter("ar_penalty_id", $arBookData->getId())
                    ->addFieldToFilter("reference_id", $referenceData["targetInvoiceId"])
                    ->getLastItem();
                if ($penalty->getId()) {
                    $arPenaltyDetails = $this->arPenaltyDetailsModel->create()->load($penalty->getId());
                }

                $arPenaltyDetails->setData('reference_id', $referenceData['targetInvoiceId']);
            }
            $arPenaltyDetails->setData('penalty_id', $this->postData['reference']);
            $arPenaltyDetails->setData('reference_type', $referenceData['type']);
            $outstandingAmount = number_format((float)$referenceData['outstandingAmount'], 2, '.', '');
            $arPenaltyDetails->setData('amount', $outstandingAmount);
            $referenceAmount = number_format((float)$referenceData['invoiceAmount'], 2, '.', '');
            $arPenaltyDetails->setData('reference_amount', $referenceAmount);
            $arPenaltyDetails->setData('comments', $referenceData['comments']);
            $arPenaltyDetails->setData('ar_penalty_id', $arBookData->getId());
            $arPenaltyDetails->save();
        }
    }

    public function deleteAdditionalCharges($arBookData)
    {
        $arPenaltyDetailsCollection = $this->arPenaltyDetailsModel->create()->getCollection()
            ->addFieldToFilter("penalty_id", $this->postData['reference'])
            ->addFieldToFilter("ar_penalty_id", $arBookData->getId())
            ->addFieldToFilter("reference_type", "additionalFee");
        $arPenaltyDetailsData = $arPenaltyDetailsCollection->getData();
        if (!empty($arPenaltyDetailsData)) {
            foreach ($arPenaltyDetailsData as $arPenaltyDetail) {
                $arPenaltyDetailModel = $this->arPenaltyDetailsModel->create()->load($arPenaltyDetail["primary_id"]);
                if ($arPenaltyDetailModel->getId()) {
                    $arPenaltyDetailModel->delete();
                }
            }
        }
    }

    public function getMagentoOrderId($gp_order_id)
    {
        if (!empty($gp_order_id)) {
            $orderData = $this->i95devOrderFactory->create()->load($gp_order_id, 'target_order_id');
            if (!$orderData->getSourceOrderId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_not_exists"));
            }

            return $orderData->getSourceOrderId();
        }
    }

    /**
     * Updating the cash receipts statuses
     * @Param object  $cashReceiptsData
     * @Return boolean
     */
    public function updateCashReceipts()
    {
        $cashReceiptNumber = $this->postData['targetInvoiceId'];
        $collection = $this->arPayment->create()->load($cashReceiptNumber, 'cash_receipt_number');
        $collectionData = $collection->getData();
        if (!empty($collectionData)) {
            $collection->setData('status', \I95DevConnect\MessageQueue\Helper\Data::COMPLETE);
            $collection->save();
        }
        return true;
    }

    /**
     * Prepare data array
     */
    public function prepareData()
    {
        $this->postData['targetInvoiceId'] = $this->dataHelper->getValueFromArray("targetInvoiceId", $this->stringData);
        $this->postData['targetCustomerId'] = $this->dataHelper->getValueFromArray(
            "targetCustomerId",
            $this->stringData
        );
        $this->postData['invoiceAmount'] = number_format((float)$this->dataHelper->getValueFromArray(
            "invoiceAmount",
            $this->stringData
        ), 2, '.', '');
        $this->postData['outstandingAmount'] = number_format((float)$this->dataHelper->getValueFromArray(
            "outstandingAmount",
            $this->stringData
        ), 2, '.', '');
        $this->postData['invoiceDate'] = strtotime($this->dataHelper->getValueFromArray(
            "documentDate",
            $this->stringData
        ));
        $this->postData['dueDate'] = $this->dataHelper->getValueFromArray("dueDate", $this->stringData);
        $this->postData['targetOrderId'] = $this->dataHelper->getValueFromArray("targetOrderId", $this->stringData);
        $this->postData['poNumber'] = $this->dataHelper->getValueFromArray("journalNumber", $this->stringData);
        $this->postData['discountDate'] = strtotime($this->dataHelper->getValueFromArray(
            "discountDate",
            $this->stringData
        ));
        $this->postData['discountAmount'] = number_format((float)$this->dataHelper->getValueFromArray(
            "discountAmount",
            $this->stringData
        ), 2, '.', '');
        $this->postData['modifiedDate'] = $this->dataHelper->getValueFromArray("modifiedDate", $this->stringData);
        $this->postData['sourceOrderId'] = $this->dataHelper->getValueFromArray("sourceOrderId", $this->stringData);
        $this->postData['type'] = $this->dataHelper->getValueFromArray("type", $this->stringData);
        $this->postData['reference'] = $this->dataHelper->getValueFromArray("reference", $this->stringData);
        $this->postData['invoices'] = $this->dataHelper->getValueFromArray("invoices", $this->stringData);
        $this->postData['penaltyTerm'] = $this->dataHelper->getValueFromArray("penaltyTerm", $this->stringData);
        $this->postData['additionalCharges'] = number_format((float)$this->dataHelper->getValueFromArray(
            "additionalCharges",
            $this->stringData
        ), 2, '.', '');
        $this->postData['penaltyCharges'] = number_format((float)$this->dataHelper->getValueFromArray(
            "penaltyCharges",
            $this->stringData
        ), 2, '.', '');

        if ($this->postData['type'] == 'penalty') {
            $this->postData['customerEntity']['targetCustomerId'] =  $this->dataHelper->getValueFromArray(
                "targetCustomerId",
                $this->stringData
            );
            if (isset($this->stringData['customer'])) {
                $this->postData['customerEntity']['targetCustomerId'] = $this->dataHelper->getValueFromArray(
                    "targetCustomerId",
                    $this->stringData['customer']
                );
                $this->postData['customerEntity']['creditLimitType'] = $this->dataHelper->getValueFromArray(
                    "creditLimitType",
                    $this->stringData['customer']
                );
                $this->postData['customerEntity']['creditLimitAmount'] = $this->dataHelper->getValueFromArray(
                    "creditLimitAmount",
                    $this->stringData['customer']
                );
                $this->postData['customerEntity']['availableLimit'] = $this->dataHelper->getValueFromArray(
                    "availableLimit",
                    $this->stringData['customer']
                );
            }
            $this->postData['customerEntity']['sourceId'] = $this->customerId;
        }
    }
}
