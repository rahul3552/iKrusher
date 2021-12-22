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
interface PaymentInterface extends \Magento\Framework\Api\CustomAttributesDataInterface //NOSONAR
{
    /*     * #@+
     * Constants defined for keys of  data array
     */

    const SOURCE_PAYMENT_ID = "sourcePaymentId";
    const SOURCE_CUSTOMER_ID = "sourceCustomerId";
    const TARGET_CUSTOMER_ID = "targetCustomerId";
    const PAYMENT_REF_NO = "paymentRefNo";
    const CASH_RECEIPT_NUMBER = "cashReceiptNumber";
    const DOCUMENT_AMOUNT = "documentAmount";
    const TRANSACTION_NUMBER = "transactionNumber";
    const NOTES = "notes";
    const USER_ENTERED = "userEntered";
    const PAYMENT_COMMENT = "paymentComment";
    const PAYMENT = "payment";
    const AR_POSTED_INVOICE = "arPostedInvoice";
    const AR_RETURNS = "arReturns";

    /**
     * get sourcePaymentId Value
     *
     * @api
     * @return string|null
     */
    public function getSourcePaymentId();

    /**
     * get sourceCustomerId Value
     *
     * @api
     * @return string|null
     */
    public function getSourceCustomerId();

    /**
     * get targetCustomerId Value
     *
     * @api
     * @return string|null
     */
    public function getTargetCustomerId();

    /**
     * get paymentRefNo Value
     *
     * @api
     * @return string|null
     */
    public function getPaymentRefNo();

    /**
     * get cashReceiptNumber Value
     *
     * @api
     * @return string|null
     */
    public function getCashReceiptNumber();

    /**
     * get documentAmount Value
     *
     * @api
     * @return string|null
     */
    public function getDocumentAmount();

    /**
     * get transactionNumber Value
     *
     * @api
     * @return string|null
     */
    public function getTransactionNumber();

    /**
     * get notes Value
     *
     * @api
     * @return string|null
     */
    public function getNotes();

    /**
     * get userEntered Value
     *
     * @api
     * @return string|null
     */
    public function getUserEntered();

    /**
     * get paymentComment Value
     *
     * @api
     * @return string|null
     */
    public function getPaymentComment();

    /**
     * get payment Value
     *
     * @api
     * @return \I95DevConnect\BillPay\Api\Data\PaymentTransInterface $paymentTransEntity|null
     */
    public function getPayment();

    /**
     * get arPostedInvoice Value
     *
     * @api
     * @return \I95DevConnect\BillPay\Api\Data\CashReceiptInterface[] $cashReceipt|null
     */
    public function getArPostedInvoice();

    /**
     * get arReturns Value
     *
     * @api
     * @return \I95DevConnect\BillPay\Api\Data\CashReceiptInterface[] $cashReceipt|null
     */
    public function getArReturns();

    /**
     * set sourcePaymentId Value
     *
     * @api
     * @param string|null $sourcePaymentId
     */
    public function setSourcePaymentId($sourcePaymentId);

    /**
     * set sourceCustomerId Value
     *
     * @api
     * @param string|null $sourceCustomerId
     */
    public function setSourceCustomerId($sourceCustomerId);

    /**
     * set targetCustomerId Value
     *
     * @api
     * @param string|null $targetCustomerId
     */
    public function setTargetCustomerId($targetCustomerId);

    /**
     * set paymentRefNo Value
     *
     * @api
     * @param string|null $paymentRefNo
     */
    public function setPaymentRefNo($paymentRefNo);

    /**
     * set cashReceiptNumber Value
     *
     * @api
     * @param string|null $cashReceiptNumber
     */
    public function setCashReceiptNumber($cashReceiptNumber);

    /**
     * set documentAmount Value
     *
     * @api
     * @param string|null $documentAmount
     */
    public function setDocumentAmount($documentAmount);

    /**
     * set transactionNumber Value
     *
     * @api
     * @param string|null $transactionNumber
     */
    public function setTransactionNumber($transactionNumber);

    /**
     * set notes Value
     *
     * @api
     * @param string|null $notes
     */
    public function setNotes($notes);

    /**
     * set userEntered Value
     *
     * @api
     * @param string|null $userEntered
     */
    public function setUserEntered($userEntered);

    /**
     * set paymentComment Value
     *
     * @api
     * @param string|null $paymentComment
     */
    public function setPaymentComment($paymentComment);

    /**
     * set payment Value
     *
     * @api
     * @param \I95DevConnect\BillPay\Api\Data\PaymentTransInterface[] $paymentTransEntity
     */
    public function setPayment($paymentTransEntity);

    /**
     * set arPostedInvoice Value
     *
     * @api
     * @param \I95DevConnect\BillPay\Api\Data\CashReceiptInterface[] $cashReceipt
     */
    public function setArPostedInvoice($cashReceipt);

    /**
     * set arReturns Value
     *
     * @api
     * @param \I95DevConnect\BillPay\Api\Data\CashReceiptInterface[] $cashReceipt
     */
    public function setArReturns($cashReceipt);
}
