<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\Data;

use I95DevConnect\BillPay\Api\Data\PaymentInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Payment extends AbstractExtensibleObject implements PaymentInterface //NOSONAR
{

    /**
     * {@inheritdoc}
     */
    public function getSourcePaymentId()
    {
        $this->_get(self::SOURCE_PAYMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCustomerId()
    {
        $this->_get(self::SOURCE_CUSTOMER_ID);
    }

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
    public function getPaymentRefNo()
    {
        $this->_get(self::PAYMENT_REF_NO);
    }

    /**
     * {@inheritdoc}
     */
    public function getCashReceiptNumber()
    {
        $this->_get(self::CASH_RECEIPT_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentAmount()
    {
        $this->_get(self::DOCUMENT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionNumber()
    {
        $this->_get(self::TRANSACTION_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotes()
    {
        $this->_get(self::NOTES);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserEntered()
    {
        $this->_get(self::USER_ENTERED);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentComment()
    {
        $this->_get(self::PAYMENT_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getPayment()
    {
        $this->_get(self::PAYMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getArPostedInvoice()
    {
        $this->_get(self::AR_POSTED_INVOICE);
    }

    /**
     * {@inheritdoc}
     */
    public function getArReturns()
    {
        $this->_get(self::AR_RETURNS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourcePaymentId($sourcePaymentId)
    {
        $this->_set(self::SOURCE_PAYMENT_ID, $sourcePaymentId);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceCustomerId($sourceCustomerId)
    {
        $this->_set(self::SOURCE_CUSTOMER_ID, $sourceCustomerId);
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
    public function setPaymentRefNo($paymentRefNo)
    {
        $this->_set(self::PAYMENT_REF_NO, $paymentRefNo);
    }

    /**
     * {@inheritdoc}
     */
    public function setCashReceiptNumber($cashReceiptNumber)
    {
        $this->_set(self::CASH_RECEIPT_NUMBER, $cashReceiptNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setDocumentAmount($documentAmount)
    {
        $this->_set(self::DOCUMENT_AMOUNT, $documentAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionNumber($transactionNumber)
    {
        $this->_set(self::TRANSACTION_NUMBER, $transactionNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setNotes($notes)
    {
        $this->_set(self::NOTES, $notes);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserEntered($userEntered)
    {
        $this->_set(self::USER_ENTERED, $userEntered);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentComment($paymentComment)
    {
        $this->_set(self::PAYMENT_COMMENT, $paymentComment);
    }

    /**
     * {@inheritdoc}
     */
    public function setPayment($payment)
    {
        $this->_set(self::PAYMENT, $payment);
    }

    /**
     * {@inheritdoc}
     */
    public function setArPostedInvoice($arPostedInvoice)
    {
        $this->_set(self::AR_POSTED_INVOICE, $arPostedInvoice);
    }

    /**
     * {@inheritdoc}
     */
    public function setArReturns($arReturns)
    {
        $this->_set(self::AR_RETURNS, $arReturns);
    }

    public function _set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }
}
