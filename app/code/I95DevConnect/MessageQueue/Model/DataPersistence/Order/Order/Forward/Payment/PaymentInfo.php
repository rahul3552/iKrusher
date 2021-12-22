<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */
namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Payment;

/**
 * Class for PaymentInfo payment method data in order result to ERP
 */
class PaymentInfo
{
    /**
     * @var PaymentEntity
     */
    public $paymentEntity;

    /**
     *
     * @param PaymentEntity $paymentEntity
     */
    public function __construct(
        PaymentEntity $paymentEntity
    ) {
        $this->paymentEntity = $paymentEntity;
    }

    /**
     * Assign payment data to payment entity
     * @param  \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     * @createdBy SravaniPolu
     */
    public function getOrderPayment($order)
    {
        $paymentEntityDetails = [];
        try {
            $paymentData = $order->getPayment();
            $paymentEntityDetails[] = $this->paymentEntity->assignPaymentData($paymentData);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        
        return $paymentEntityDetails;
    }
}
