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
interface PaymentTransInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /*     * #@+
     * Constants defined for keys of  data array
     */

    const PAYMENT_NAME = "paymentName";
    const CREDIT_CARD_NUMBER = "creditCardNumber";
    const CVV = "cvv";
    const EXPIRY_MONTH = "expiryMonth";
    const EXPIRY_YEAR = "expiryYear";
    const PAYMENT_AMOUNT = "paymentAmount";
    const CARD_TYPE = "cardType";
    const TRANSACTION_NUMBER = "transactionNumber";
    const USER_DEFINED= "userDefined";

    /**
     * get paymentName Value
     *
     * @api
     * @return string|null
     */
    public function getPaymentName();
    
    /**
     * get creditCardNumber Value
     *
     * @api
     * @return string|null
     */
    public function getCreditCardNumber();
    
    /**
     * get cvv Value
     *
     * @api
     * @return string|null
     */
    public function getCvv();
    
    /**
     * get expiryMonth Value
     *
     * @api
     * @return string|null
     */
    public function getExpiryMonth();
    
    /**
     * get expiryYear Value
     *
     * @api
     * @return string|null
     */
    public function getExpiryYear();
    
    /**
     * get paymentAmount Value
     *
     * @api
     * @return string|null
     */
    public function getPaymentAmount();
    
    /**
     * get cardType Value
     *
     * @api
     * @return string|null
     */
    public function getCardType();
    
    /**
     * get transactionNumber Value
     *
     * @api
     * @return string|null
     */
    public function getTransactionNumber();
    
    /**
     * get userDefined Value
     *
     * @api
     * @return string|null
     */
    public function getUserDefined();
    
    /**
     * set paymentName Value
     *
     * @api
     * @param string|null $paymentName
     */
    public function setPaymentName($paymentName);
    
    /**
     * set creditCardNumber Value
     *
     * @api
     * @param string|null $creditCardNumber
     */
    public function setCreditCardNumber($creditCardNumber);
    
    /**
     * set cvv Value
     *
     * @api
     * @param string|null $cvv
     */
    public function setCvv($cvv);
    
    /**
     * set expiryMonth Value
     *
     * @api
     * @param string|null $expiryMonth
     */
    public function setExpiryMonth($expiryMonth);
    
    /**
     * set expiryYear Value
     *
     * @api
     * @param string|null $expiryYear
     */
    public function setExpiryYear($expiryYear);
    
    /**
     * set paymentAmount Value
     *
     * @api
     * @param string|null $paymentAmount
     */
    public function setPaymentAmount($paymentAmount);
    
    /**
     * set cardType Value
     *
     * @api
     * @param string|null $cardType
     */
    public function setCardType($cardType);
    
    /**
     * set transactionNumber Value
     *
     * @api
     * @param string|null $transactionNumber
     */
    public function setTransactionNumber($transactionNumber);
    
    /**
     * set userDefined Value
     *
     * @api
     * @param string|null $userDefined
     */
    public function setUserDefined($userDefined);
}
