<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Api\Data;

/**
 * @api
 */
interface CashReceiptInterface extends \Magento\Framework\Api\CustomAttributesDataInterface //NOSONAR
{
    /*     * #@+
     * Constants defined for keys of  data array
     */

    const TARGET_CUSTOMER_ID = "targetCustomerId";
    const MODIFIED_DATE = "modifiedDate";
    const INVOICE_DATE = "invoiceDate";
    const APPLIED_DOC_TOTAL_AMOUNT = "appliedDocTotalAmount";
    const APPLIED_DOC_UNAPPLIED_AMOUNT = "appliedDocUnappliedAmount";
    const APPLIED_DOC_APPLIED_AMOUNT = "appliedDocAppliedAmount";
    const APPLIED_DOCUMENT_TYPE = "appliedDocumentType";
    const APPLIED_DOCUMENT_NUMBER = "appliedDocumentNumber";
    const RECEIPT_DOCUMENT_AMOUNT = "receiptDocumentAmount";
    const RECEIPT_DOCUMENT_TYPE = "receiptDocumentType";
    const RECEIPT_UNAPPLIED_AMOUNT = "receiptUnappliedAmount";
    const RECEIPT_APPLIED_AMOUNT = "receiptAppliedAmount";
    const RECEIPT_DOCUMENT_NUMBER = "receiptDocumentNumber";
    const PAYMENT_TYPE = "paymentType";
    const TRANSACTION_ID = "transactionId";
    const CARD_TYPE = "cardType";
    const INVOICE_STATUS = "invoiceStatus";
    const PAYMENT_COMMENTS = "paymentComments";
    const SOURCE_INVOICE_ID = 'sourceInvoiceId';
    const TARGET_ORDER_ID = 'targetOrderId';
    const DISCOUNT_AMOUNT = "discountAmount";
    const PENALTY_AMOUNT = "penaltyAmount";
    const RETURN_DOCUMENT_DATE = "returnDocumentDate";
    const RETURN_ADJUSTMENT_AMOUNT = "returnAdjustmentAmount";
    const CUSTOMER_ENTITY = "customerEntity";

    /**
     * get targetCustomerId Value
     *
     * @api
     * @return string|null
     */
    public function getTargetCustomerId();

    /**
     * get modifiedDate Value
     *
     * @api
     * @return string|null
     */
    public function getModifiedDate();

    /**
     * get invoiceDate Value
     *
     * @api
     * @return string|null
     */
    public function getInvoiceDate();

    /**
     * get appliedDocTotalAmount Value
     *
     * @api
     * @return string|null
     */
    public function getAppliedDocTotalAmount();

    /**
     * get appliedDocUnappliedAmount Value
     *
     * @api
     * @return string|null
     */
    public function getAppliedDocUnappliedAmount();

    /**
     * get appliedDocAppliedAmount Value
     *
     * @api
     * @return string|null
     */
    public function getAppliedDocAppliedAmount();

    /**
     * get appliedDocumentType Value
     *
     * @api
     * @return string|null
     */
    public function getAppliedDocumentType();

    /**
     * get appliedDocumentNumber Value
     *
     * @api
     * @return string|null
     */
    public function getAppliedDocumentNumber();

    /**
     * get receiptDocumentAmount Value
     *
     * @api
     * @return string|null
     */
    public function getReceiptDocumentAmount();

    /**
     * get receiptDocumentType Value
     *
     * @api
     * @return string|null
     */
    public function getReceiptDocumentType();

    /**
     * get receiptUnappliedAmount Value
     *
     * @api
     * @return string|null
     */
    public function getReceiptUnappliedAmount();

    /**
     * get receiptAppliedAmount Value
     *
     * @api
     * @return string|null
     */
    public function getReceiptAppliedAmount();

    /**
     * get receiptDocumentNumber Value
     *
     * @api
     * @return string|null
     */
    public function getReceiptDocumentNumber();

    /**
     * get paymentType Value
     *
     * @api
     * @return string|null
     */
    public function getPaymentType();

    /**
     * get transactionId Value
     *
     * @api
     * @return string|null
     */
    public function getTransactionId();

    /**
     * get cardType Value
     *
     * @api
     * @return string|null
     */
    public function getCardType();

    /**
     * get invoiceStatus Value
     *
     * @api
     * @return string|null
     */
    public function getInvoiceStatus();

    /**
     * get paymentComments Value
     *
     * @api
     * @return string|null
     */
    public function getPaymentComments();

    /**
     * get sourceInvoiceId Value
     *
     * @api
     * @return string|null
     */
    public function getSourceInvoiceId();

    /**
     * get targetOrderId Value
     *
     * @api
     * @return string|null
     */
    public function getTargetOrderId();

    /**
     * get discountAmount Value
     *
     * @api
     * @return string|null
     */
    public function getDiscountAmount();

    /**
     * get penaltyAmount Value
     *
     * @api
     * @return string|null
     */
    public function getPenaltyAmount();

    /**
     * get returnDocumentDate Value
     *
     * @api
     * @return string|null
     */
    public function getReturnDocumentDate();

    /**
     * get returnAdjustmentAmount Value
     *
     * @api
     * @return string|null
     */
    public function getReturnAdjustmentAmount();

    /**
     * set targetCustomerId Value
     *
     * @api
     * @param string|null $targetCustomerId
     */
    public function setTargetCustomerId($targetCustomerId);

    /**
     * set modifiedDate Value
     *
     * @api
     * @param string|null $modifiedDate
     */
    public function setModifiedDate($modifiedDate);

    /**
     * set invoiceDate Value
     *
     * @api
     * @param string|null $invoiceDate
     */
    public function setInvoiceDate($invoiceDate);

    /**
     * get appliedDocTotalAmount Value
     *
     * @api
     * @param string|null $appliedDocTotalAmount
     */
    public function setAppliedDocTotalAmount($appliedDocTotalAmount);

    /**
     * get appliedDocUnappliedAmount Value
     *
     * @api
     * @param string|null $appliedDocUnappliedAmount
     */
    public function setAppliedDocUnappliedAmount($appliedDocUnappliedAmount);

    /**
     * set appliedDocAppliedAmount Value
     *
     * @api
     * @param string|null $appliedDocAppliedAmount
     */
    public function setAppliedDocAppliedAmount($appliedDocAppliedAmount);

    /**
     * set appliedDocumentType Value
     *
     * @api
     * @param string|null $appliedDocumentType
     */
    public function setAppliedDocumentType($appliedDocumentType);

    /**
     * set appliedDocumentNumber Value
     *
     * @api
     * @param string|null $appliedDocumentNumber
     */
    public function setAppliedDocumentNumber($appliedDocumentNumber);

    /**
     * set receiptDocumentAmount Value
     *
     * @api
     * @param string|null $receiptDocumentAmount
     */
    public function setReceiptDocumentAmount($receiptDocumentAmount);

    /**
     * set receiptDocumentType Value
     *
     * @api
     * @param string|null $receiptDocumentType
     */
    public function setReceiptDocumentType($receiptDocumentType);

    /**
     * set receiptUnappliedAmount Value
     *
     * @api
     * @param string|null $receiptUnappliedAmount
     */
    public function setReceiptUnappliedAmount($receiptUnappliedAmount);

    /**
     * set receiptAppliedAmount Value
     *
     * @api
     * @param string|null $receiptAppliedAmount
     */
    public function setReceiptAppliedAmount($receiptAppliedAmount);

    /**
     * set receiptDocumentNumber Value
     *
     * @api
     * @param string|null $receiptDocumentNumber
     */
    public function setReceiptDocumentNumber($receiptDocumentNumber);

    /**
     * set paymentType Value
     *
     * @api
     * @param string|null $paymentType
     */
    public function setPaymentType($paymentType);

    /**
     * set transactionId Value
     *
     * @api
     * @param string|null $transactionId
     */
    public function setTransactionId($transactionId);

    /**
     * set cardType Value
     *
     * @api
     * @param string|null $cardType
     */
    public function setCardType($cardType);

    /**
     * set invoiceStatus Value
     *
     * @api
     * @param string|null $invoiceStatus
     */
    public function setInvoiceStatus($invoiceStatus);

    /**
     * set paymentComments Value
     *
     * @api
     * @param string|null $paymentComments
     */
    public function setPaymentComments($paymentComments);

    /**
     * set sourceInvoiceId Value
     *
     * @api
     * @param string|null $sourceInvoiceId
     */
    public function setSourceInvoiceId($sourceInvoiceId);

    /**
     * set targetOrderId Value
     *
     * @api
     * @param string|null $targetOrderId
     */
    public function setTargetOrderId($targetOrderId);

    /**
     * set discountAmount Value
     *
     * @api
     * @param string|null $discountAmount
     */
    public function setDiscountAmount($discountAmount);

    /**
     * set penaltyAmount Value
     *
     * @api
     * @param string|null $penaltyAmount
     */
    public function setPenaltyAmount($penaltyAmount);

    /**
     * set returnDocumentDate Value
     *
     * @api
     * @param string|null $returnDocumentDate
     */
    public function setReturnDocumentDate($returnDocumentDate);

    /**
     * set returnAdjustmentAmount Value
     *
     * @api
     * @param string|null $returnAdjustmentAmount
     */
    public function setReturnAdjustmentAmount($returnAdjustmentAmount);

    /**
     * set customerEntity Value
     *
     * @api
     * @param \I95DevConnect\MessageQueue\Api\Data\CustomerInterface[] $customerEntity
     */
    public function setCustomerEntity($customerEntity);

    /**
     * get customerEntity Value
     *
     * @api
     * @return \I95DevConnect\MessageQueue\Api\Data\CustomerInterface $customerEntity|null
     */
    public function getCustomerEntity();
}
