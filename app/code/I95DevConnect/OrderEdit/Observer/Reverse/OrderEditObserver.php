<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_OrderEdit
 */
namespace I95DevConnect\OrderEdit\Observer\Reverse;

use \Magento\Framework\Event\ObserverInterface;

/**
 * Observer for edit order
 */
class OrderEditObserver implements ObserverInterface
{
    const CUSTOMER = 'customer';
    protected $i95Order = null;

    /**
     * Add parent order details in quoteModel
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->i95Order = $observer->getData('quoteObject');
        if (isset($this->i95Order->stringData['isEditOrder'])) {
            if (isset($this->i95Order->stringData[self::CUSTOMER]['isGuest'])) {
                $this->addGuestCustomerDetails($this->i95Order->stringData);
            }
            $parentOrder = $this->i95Order->stringData['parentOrder'];
            if ($parentOrder->getId()) {
                $originalId = $parentOrder->getOriginalIncrementId();
                if (!$originalId) {
                    $originalId = $parentOrder->getIncrementId();
                }
                $this->i95Order->orderData = [
                    'original_increment_id' => $originalId,
                    'relation_parent_id' => $parentOrder->getId(),
                    'relation_parent_real_id' => $parentOrder->getIncrementId(),
                    'edit_increment' => $parentOrder->getEditIncrement() + 1,
                    'increment_id' => $originalId . '-' . ($parentOrder->getEditIncrement() + 1)
                ];
                $this->i95Order->quoteModel->setReservedOrderId($this->i95Order->orderData['increment_id']);
                if ($parentOrder->getPayment()->getMethod() == 'authnetcim') {
                    $this->updateTokenId($parentOrder);
                }
            }
        }
    }

    /**
     * Re-generate Token and assign to payment data.
     * @param obj $parentOrder
     */
    public function updateTokenId($parentOrder)
    {
        $parentOrderPayment = $parentOrder->getPayment();
        $cardId = $parentOrderPayment->getTokenbaseId();
        $this->i95Order->quoteModel->getPayment()->setMethod($parentOrderPayment->getMethod());
        $this->i95Order->quoteModel->getPayment()->setTokenbaseId($cardId);
        $this->i95Order->quoteModel->getPayment()->setCcType($parentOrderPayment->getCcType());
        $this->i95Order->quoteModel->getPayment()->setCcNumber($parentOrderPayment->getCcNumber());
        $this->i95Order->quoteModel->getPayment()->setCcExpMonth($parentOrderPayment->getCcExpMonth());
        $this->i95Order->quoteModel->getPayment()->setCcExpYear($parentOrderPayment->getCcExpYear());
    }

    public function addGuestCustomerDetails($stringData)
    {
        $customerEmail = '';
        if (isset($stringData[self::CUSTOMER]['email'])) {
            $customerEmail = $stringData[self::CUSTOMER]['email'];
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_quote_customer_count"));
        }
        $this->i95Order->quoteModel->setStoreId(1)
                ->setCheckoutMethod('guest')
                ->setCustomerId(null)
                ->setCustomerEmail($customerEmail)
                ->setCustomerIsGuest(true)
                ->setCustomerGroupId(0);
    }
}
