<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */
namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward;

/**
 * Class responsible for preparing order discount data which will be added in order result to ERP
 */
class DiscountEntity
{

    /**
     * Returns discount data from order
     * @param  \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     * @createdBy SravaniPolu
     */
    public function getOrderDiscount($order)
    {
        try {
            if ($order->getBaseDiscountAmount()) {
                $discountEntity['discountType'] = 'discount';
                $discountEntity['discountAmount'] = (float)(abs($order->getBaseDiscountAmount()));
                $discountEntity['discountCode'] = $order->getCouponCode();
                $discountData[] = $discountEntity;
            } else {
                $discountData = [];
            }
            
            return $discountData;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
