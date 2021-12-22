<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\Data;

use I95DevConnect\BillPay\Api\Data\AccountReceivablesInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class AccountReceivables extends AbstractExtensibleObject implements AccountReceivablesInterface //NOSONAR
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
    public function getTargetInvoiceId()
    {
        $this->_get(self::TARGET_INVOICE_ID);
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
    public function getSourceOrderId()
    {
        $this->_get(self::SOURCE_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceAmount()
    {
        $this->_get(self::INVOICE_AMOUNT);
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
    public function getUnappliedAmount()
    {
        $this->_get(self::UNAPPLIED_AMOUNT);
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
    public function getAppliedAmount()
    {
        $this->_get(self::APPLIED_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getPoNumber()
    {
        $this->_get(self::PO_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        $this->_get(self::TYPE);
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
    public function getDiscountDate()
    {
        $this->_get(self::DISCOUNT_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function getArCustomerEntity()
    {
        $this->_get(self::AR_CUSTOMER_ENTITY);
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
    public function setTargetInvoiceId($targetInvoiceId)
    {
        $this->_set(self::TARGET_INVOICE_ID, $targetInvoiceId);
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
    public function setSourceOrderId($sourceOrderId)
    {
        $this->_set(self::SOURCE_ORDER_ID, $sourceOrderId);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceAmount($invoiceAmount)
    {
        $this->_set(self::INVOICE_AMOUNT, $invoiceAmount);
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
    public function setUnappliedAmount($unappliedAmount)
    {
        $this->_set(self::UNAPPLIED_AMOUNT, $unappliedAmount);
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
    public function setAppliedAmount($appliedAmount)
    {
        $this->_set(self::APPLIED_AMOUNT, $appliedAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function setPoNumber($poNumber)
    {
        $this->_set(self::PO_NUMBER, $poNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->_set(self::TYPE, $type);
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
    public function setDiscountDate($discountDate)
    {
        $this->_set(self::DISCOUNT_DATE, $discountDate);
    }

    /**
     * {@inheritdoc}
     */
    public function setArCustomerEntity($customerEntity)
    {
        $this->_set(self::AR_CUSTOMER_ENTITY, $customerEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function getDueDate()
    {
        $this->_get(self::DUE_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDueDate($dueDate)
    {
        $this->_set(self::DUE_DATE, $dueDate);
    }

    public function _set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }
}
