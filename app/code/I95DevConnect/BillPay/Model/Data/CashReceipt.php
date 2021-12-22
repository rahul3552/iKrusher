<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\Data;

use I95DevConnect\BillPay\Api\Data\CashReceiptInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class CashReceipt extends AbstractExtensibleObject implements CashReceiptInterface //NOSONAR
{

    /**
     * {@inheritdoc}
     */
    public function getTargetCustomerId()
    {
        $this->_get(self::TARGET_CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedDate()
    {
        $this->_get(self::MODIFIED_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceDate()
    {
        $this->_get(self::INVOICE_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedDocTotalAmount()
    {
        $this->_get(self::APPLIED_DOC_TOTAL_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedDocUnappliedAmount()
    {
        $this->_get(self::APPLIED_DOC_UNAPPLIED_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedDocAppliedAmount()
    {
        $this->_get(self::APPLIED_DOC_APPLIED_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedDocumentType()
    {
        $this->_get(self::APPLIED_DOCUMENT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedDocumentNumber()
    {
        $this->_get(self::APPLIED_DOCUMENT_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getReceiptDocumentAmount()
    {
        $this->_get(self::RECEIPT_DOCUMENT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getReceiptDocumentType()
    {
        $this->_get(self::RECEIPT_DOCUMENT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getReceiptUnappliedAmount()
    {
        $this->_get(self::RECEIPT_UNAPPLIED_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getReceiptAppliedAmount()
    {
        $this->_get(self::RECEIPT_APPLIED_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getReceiptDocumentNumber()
    {
        $this->_get(self::RECEIPT_DOCUMENT_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentType()
    {
        $this->_get(self::PAYMENT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        $this->_get(self::TRANSACTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getCardType()
    {
        $this->_get(self::CARD_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceStatus()
    {
        $this->_get(self::INVOICE_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentComments()
    {
        $this->_get(self::PAYMENT_COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceInvoiceId()
    {
        $this->_get(self::SOURCE_INVOICE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetOrderId()
    {
        $this->_get(self::TARGET_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountAmount()
    {
        $this->_get(self::DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getPenaltyAmount()
    {
        $this->_get(self::PENALTY_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnDocumentDate()
    {
        $this->_get(self::RETURN_DOCUMENT_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnAdjustmentAmount()
    {
        $this->_get(self::RETURN_ADJUSTMENT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetCustomerId($targetCustomerId)
    {
        $this->_set(self::TARGET_CUSTOMER_ID, $targetCustomerId);
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->_set(self::MODIFIED_DATE, $modifiedDate);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceDate($invoiceDate)
    {
        $this->_set(self::INVOICE_DATE, $invoiceDate);
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedDocTotalAmount($appliedDocTotalAmount)
    {
        $this->_set(self::APPLIED_DOC_TOTAL_AMOUNT, $appliedDocTotalAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedDocUnappliedAmount($appliedDocUnappliedAmount)
    {
        $this->_set(self::APPLIED_DOC_UNAPPLIED_AMOUNT, $appliedDocUnappliedAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedDocAppliedAmount($appliedDocAppliedAmount)
    {
        $this->_set(self::APPLIED_DOC_APPLIED_AMOUNT, $appliedDocAppliedAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedDocumentType($appliedDocumentType)
    {
        $this->_set(self::APPLIED_DOCUMENT_TYPE, $appliedDocumentType);
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedDocumentNumber($appliedDocumentNumber)
    {
        $this->_set(self::APPLIED_DOCUMENT_NUMBER, $appliedDocumentNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setReceiptDocumentAmount($receiptDocumentAmount)
    {
        $this->_set(self::RECEIPT_DOCUMENT_AMOUNT, $receiptDocumentAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setReceiptDocumentType($receiptDocumentType)
    {
        $this->_set(self::RECEIPT_DOCUMENT_TYPE, $receiptDocumentType);
    }

    /**
     * {@inheritdoc}
     */
    public function setReceiptUnappliedAmount($receiptUnappliedAmount)
    {
        $this->_set(self::RECEIPT_UNAPPLIED_AMOUNT, $receiptUnappliedAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setReceiptAppliedAmount($receiptAppliedAmount)
    {
        $this->_set(self::RECEIPT_APPLIED_AMOUNT, $receiptAppliedAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setReceiptDocumentNumber($receiptDocumentNumber)
    {
        $this->_set(self::RECEIPT_DOCUMENT_NUMBER, $receiptDocumentNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentType($paymentType)
    {
        $this->_set(self::PAYMENT_TYPE, $paymentType);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionId($transactionId)
    {
        $this->_set(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function setCardType($cardType)
    {
        $this->_set(self::CARD_TYPE, $cardType);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceStatus($invoiceStatus)
    {
        $this->_set(self::INVOICE_STATUS, $invoiceStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentComments($paymentComments)
    {
        $this->_set(self::PAYMENT_COMMENTS, $paymentComments);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceInvoiceId($sourceInvoiceId)
    {
        $this->_set(self::SOURCE_INVOICE_ID, $sourceInvoiceId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetOrderId($targetOrderId)
    {
        $this->_set(self::TARGET_ORDER_ID, $targetOrderId);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->_set(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setPenaltyAmount($penaltyAmount)
    {
        $this->_set(self::PENALTY_AMOUNT, $penaltyAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setReturnDocumentDate($returnDocumentDate)
    {
        $this->_set(self::RETURN_DOCUMENT_DATE, $returnDocumentDate);
    }

    /**
     * {@inheritdoc}
     */
    public function setReturnAdjustmentAmount($returnAdjustmentAmount)
    {
        $this->_set(self::RETURN_ADJUSTMENT_AMOUNT, $returnAdjustmentAmount);
    }

    public function _set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEntity()
    {
        $this->_get(self::CUSTOMER_ENTITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerEntity($customerEntity)
    {
        $this->_set(self::CUSTOMER_ENTITY, $customerEntity);
    }
}
