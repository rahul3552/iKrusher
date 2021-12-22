<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Payment;

/**
 * Class for Free payment method data in order result to ERP
 */
class PaymentMethod
{
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
            $paymentEntity['paymentMethod'] = $paymentData->getMethod();
            return $paymentEntity;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
