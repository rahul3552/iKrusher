<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Payment;

/**
 * Class for Charge Logic method data in order result to ERP
 */
class ChargeLogic
{

    /**
     * var data
     */
    public $_logger;
    public $paymentEntity = [];

    /**
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * Assign payment data to payment entity
     *
     * @param  object $paymentData
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    public function assignPaymentData($paymentData)
    {
        try {
            $this->paymentEntity['paymentMethod'] = $paymentData->getMethod();
            $this->paymentEntity['ccNumber'] = $paymentData->getCcNumberEnc();
            $this->paymentEntity['ccExpMonth'] = $paymentData->getCcExpMonth();
            $this->paymentEntity['ccExpYear'] = $paymentData->getCcExpYear();
            $this->paymentEntity['ccType'] = $paymentData->getCcType();
            $this->paymentEntity['transactionNumber'] = $paymentData->getLastTransId();
            
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return $this->paymentEntity;
    }
}
