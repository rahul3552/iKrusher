<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * @api
 */
interface AccountReceivablesInterface extends CustomAttributesDataInterface //NOSONAR
{
    /*     * #@+
     * Constants defined for keys of  data array
     */

    const TARGET_CUSTOMER_ID = "targetCustomerId";
    const TARGET_INVOICE_ID = 'targetInvoiceId';
    const TARGET_ORDER_ID = 'targetOrderId';
    const SOURCE_ORDER_ID = "sourceOrderId";
    const INVOICE_AMOUNT = "invoiceAmount";
    const MODIFIED_DATE = "modifiedDate";
    const INVOICE_DATE = "invoiceDate";
    const UNAPPLIED_AMOUNT = "unappliedAmount";
    const INVOICE_STATUS = "invoiceStatus";
    const APPLIED_AMOUNT = "appliedAmount";
    const PO_NUMBER = "poNumber";
    const TYPE = "type";
    const DISCOUNT_AMOUNT = "discountAmount";
    const PENALTY_AMOUNT = "penaltyAmount";
    const DISCOUNT_DATE = "discountDate";
    const AR_CUSTOMER_ENTITY = "arCustomerEntity";
    const DUE_DATE = "dueDate";

    /**
     * get targetCustomerId Value
     *
     * @api
     * @return string|null
     */
    public function getTargetCustomerId();

    /**
     * get targetInvoiceId Value
     *
     * @api
     * @return string|null
     */
    public function getTargetInvoiceId();

    /**
     * get targetOrderId Value
     *
     * @api
     * @return string|null
     */
    public function getTargetOrderId();

    /**
     * get sourceOrderId Value
     *
     * @api
     * @return string|null
     */
    public function getSourceOrderId();

    /**
     * get invoiceAmount Value
     *
     * @api
     * @return string|null
     */
    public function getInvoiceAmount();

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
     * get unappliedAmount Value
     *
     * @api
     * @return string|null
     */
    public function getUnappliedAmount();

    /**
     * get invoiceStatus Value
     *
     * @api
     * @return string|null
     */
    public function getInvoiceStatus();

    /**
     * get appliedAmount Value
     *
     * @api
     * @return string|null
     */
    public function getAppliedAmount();

    /**
     * get poNumber Value
     *
     * @api
     * @return string|null
     */
    public function getPoNumber();

    /**
     * get type Value
     *
     * @api
     * @return string|null
     */
    public function getType();

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
     * get discountDate Value
     *
     * @api
     * @return string|null
     */
    public function getDiscountDate();

    /**
     * get customerEntity Value
     *
     * @api
     * @return \I95DevConnect\MessageQueue\Api\Data\CustomerInterface $customerEntity|null
     */
    public function getArCustomerEntity();

    /**
     * set targetCustomerId Value
     *
     * @api
     * @param string|null $targetCustomerId
     */
    public function setTargetCustomerId($targetCustomerId);

    /**
     * set targetInvoiceId Value
     *
     * @api
     * @param string|null $targetInvoiceId
     */
    public function setTargetInvoiceId($targetInvoiceId);

    /**
     * set targetOrderId Value
     *
     * @api
     * @param string|null $targetOrderId
     */
    public function setTargetOrderId($targetOrderId);

    /**
     * set sourceOrderId Value
     *
     * @api
     * @param string|null $sourceOrderId
     */
    public function setSourceOrderId($sourceOrderId);

    /**
     * set invoiceAmount Value
     *
     * @api
     * @param string|null $invoiceAmount
     */
    public function setInvoiceAmount($invoiceAmount);

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
     * set unappliedAmount Value
     *
     * @api
     * @param string|null $unappliedAmount
     */
    public function setUnappliedAmount($unappliedAmount);

    /**
     * set invoiceStatus Value
     *
     * @api
     * @param string|null $invoiceStatus
     */
    public function setInvoiceStatus($invoiceStatus);

    /**
     * set appliedAmount Value
     *
     * @api
     * @param string|null $appliedAmount
     */
    public function setAppliedAmount($appliedAmount);

    /**
     * set poNumber Value
     *
     * @api
     * @param string|null $poNumber
     */
    public function setPoNumber($poNumber);

    /**
     * set type Value
     *
     * @api
     * @param string|null $type
     */
    public function setType($type);

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
     * set discountDate Value
     *
     * @api
     * @param string|null $discountDate
     */
    public function setDiscountDate($discountDate);

    /**
     * set customerEntity Value
     *
     * @api
     * @param \I95DevConnect\MessageQueue\Api\Data\CustomerInterface[] $customerEntity
     */
    public function setArCustomerEntity($customerEntity);

    /**
     * set dueDate Value
     *
     * @api
     * @param string|null $dueDate
     */
    public function setDueDate($dueDate);

    /**
     * get dueDate Value
     *
     * @api
     * @return string|null
     */
    public function getDueDate();
}
