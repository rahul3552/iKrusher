<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Payment;

/**
 * Class for Checkmo payment method data in order result to ERP
 */
class Checkmo
{
    /**
     * @var I95DevConnect\MessageQueue\Model\ChequeNumber
     */
    public $chequeNumberModel;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Model\ChequeNumberFactory $chequeNumber
     */
    public function __construct(\I95DevConnect\MessageQueue\Model\ChequeNumberFactory $chequeNumber)
    {
        $this->chequeNumberModel = $chequeNumber;
    }

    /**
     * Assign payment data to payment entity
     *
     * @param  \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     * @createdBy SravaniPolu
     */
    public function assignPaymentData($payment)
    {
        try {
            $orderId = $payment->getParentId();
            $chequeNumber = $this->chequeNumberModel->create()->getCollection()
                ->addFieldToSelect('target_cheque_number')
                ->addFieldToFilter('source_order_id', $orderId);
            $chequeNumber->getSelect()->limit(1);
            $chequeNumber = $chequeNumber->getData();
            if (isset($chequeNumber[0])) {
                $chequeNumber = $chequeNumber[0]['target_cheque_number'];
            } else {
                $chequeNumber = null;
            }
            $paymentEntity['paymentMethod'] = $payment->getMethod();
            $paymentEntity['chequeNumber'] = $chequeNumber;
            $paymentEntity['transactionNumber'] = $chequeNumber;
            return $paymentEntity;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
