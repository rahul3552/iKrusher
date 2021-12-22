<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\Data;

class PaymentTrans extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \I95DevConnect\BillPay\Api\Data\PaymentTransInterface
{
   
    /**
     * {@inheritdoc}
     */
    public function getPaymentName()
    {
        $this->_get(self::PAYMENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreditCardNumber()
    {
        $this->_get(self::CREDIT_CARD_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getCvv()
    {
        $this->_get(self::CVV);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryMonth()
    {
        $this->_get(self::EXPIRY_MONTH);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryYear()
    {
        $this->_get(self::EXPIRY_YEAR);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentAmount()
    {
        $this->_get(self::PAYMENT_AMOUNT);
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
    public function getTransactionNumber()
    {
        $this->_get(self::TRANSACTION_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserDefined()
    {
        $this->_get(self::USER_DEFINED);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentName($paymentName)
    {
        $this->_set(self::PAYMENT_NAME, $paymentName);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreditCardNumber($creditCardNumber)
    {
        $this->_set(self::CREDIT_CARD_NUMBER, $creditCardNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setCvv($cvv)
    {
        $this->_set(self::CVV, $cvv);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryMonth($expiryMonth)
    {
        $this->_set(self::EXPIRY_MONTH, $expiryMonth);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryYear($expiryYear)
    {
        $this->_set(self::EXPIRY_YEAR, $expiryYear);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->_set(self::PAYMENT_AMOUNT, $paymentAmount);
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
    public function setTransactionNumber($transactionNumber)
    {
        $this->_set(self::TRANSACTION_NUMBER, $transactionNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserDefined($userDefined)
    {
        $this->_set(self::USER_DEFINED, $userDefined);
    }

    public function _set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }
}
