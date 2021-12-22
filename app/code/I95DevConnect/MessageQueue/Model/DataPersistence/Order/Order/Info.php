<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order;

use \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\ShippingAddress;
use \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Payment\PaymentInfo;

/**
 * Class for preparing order result data to be sent to ERP
 */
class Info
{
    const SHIPPINGMETHOD = "shippingMethod";
    const TARGETCUSTOMERID = "targetCustomerId";
    public $orderRepo;
    public $eventManager;
    public $customerEntity;
    public $billingAddressEntity;
    public $shippingAddressEntity;
    public $orderItemsEntity;
    public $orderPaymentEntity;
    public $orderCommentsEntity;
    public $discountEntity;
    public $order;
    public $orderId;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    public $InfoData = [];

    /**
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterfaceFactory $orderRepo
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Customer $customerEntity
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\BillingAddress $billingAddressEntity
     * @param ShippingAddress $shippingAddressEntity
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\OrderItems $orderItemsEntity
     * @param PaymentInfo $orderPaymentEntity
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\OrderComments $orderCommentsEntity
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\DiscountEntity $discountEntity
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterfaceFactory $orderRepo,
        \Magento\Framework\Event\Manager $eventManager,
        Forward\Customer $customerEntity,
        Forward\BillingAddress $billingAddressEntity,
        ShippingAddress $shippingAddressEntity,
        Forward\OrderItems $orderItemsEntity,
        PaymentInfo $orderPaymentEntity,
        Forward\OrderComments $orderCommentsEntity,
        Forward\DiscountEntity $discountEntity,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepo = $orderRepo;
        $this->eventManager = $eventManager;
        $this->customerEntity = $customerEntity;
        $this->billingAddressEntity = $billingAddressEntity;
        $this->shippingAddressEntity = $shippingAddressEntity;
        $this->orderItemsEntity = $orderItemsEntity;
        $this->orderPaymentEntity = $orderPaymentEntity;
        $this->orderCommentsEntity = $orderCommentsEntity;
        $this->discountEntity = $discountEntity;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Method to process order information to ERP
     *
     * @param int $orderId
     * @return array
     * @throws \Exception
     * @throws \Exception
     * @createdBy Sravani Polu
     */
    public function getInfo($orderId)
    {
        try {
            $this->orderId = $orderId;
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('increment_id', $orderId, 'eq')
                ->create();
            $orderCollection = $this->orderRepo->create()->getList($searchCriteria);
            $this->order = $orderCollection->getFirstItem();
            if (is_object($this->order)) {
                $this->InfoData['sourceId'] = $this->order->getIncrementId();
                $this->InfoData['taxAmount'] = $this->order->getBaseTaxAmount();
                $this->InfoData['shippingAmount'] = $this->order->getBaseShippingAmount();
                /** @author Sravani Polu-Added subtotal for order as to fix  shipping charges sync issue in AX build.*/
                $this->InfoData['subTotal'] = (float)$this->order->getBaseSubTotal();
                $this->InfoData['reference'] = $this->order->getCustomerEmail();
                $this->InfoData['orderCreatedDate'] = $this->order->getCreatedAt();
                $this->InfoData['lastUpdatedDate'] = $this->order->getUpdatedAt();
                $customer = $this->customerEntity->getCustomerEntity($this->order);
                $billingAddress = $this->billingAddressEntity->getBillingAddress($this->order);
                $shippingAddress = $this->shippingAddressEntity->getShippingAddress($this->order);
                $orderItems = $this->orderItemsEntity->getOrderItemEntities($this->order);
                $orderPayment = $this->orderPaymentEntity->getOrderPayment($this->order);
                $orderComments = $this->orderCommentsEntity->getOrderComments($this->order->getEntityId());
                $discount = $this->discountEntity->getOrderDiscount($this->order);
                $this->InfoData['orderDocumentAmount'] = $this->order->getGrandTotal();
                $this->InfoData[self::TARGETCUSTOMERID] = $customer[self::TARGETCUSTOMERID];
                $this->InfoData['customer'] = $customer;
                if (!empty($billingAddress)) {
                    $billingAddress[self::TARGETCUSTOMERID] = $customer[self::TARGETCUSTOMERID];
                    $this->InfoData['targetBillingAddressId'] = $billingAddress['targetId'];
                }
                $this->InfoData['billingAddress'] = $billingAddress;
                if (!empty($shippingAddress)) {
                    $shippingAddress[self::TARGETCUSTOMERID] = $customer[self::TARGETCUSTOMERID];
                    $this->InfoData[self::SHIPPINGMETHOD] = $shippingAddress[self::SHIPPINGMETHOD];
                    unset($shippingAddress[self::SHIPPINGMETHOD]);
                    $this->InfoData['targetShippingAddressId'] = $shippingAddress['targetId'];
                    $this->InfoData['shippingAddress'] = $shippingAddress;
                }

                $this->InfoData['orderItems'] = $orderItems;
                $this->InfoData['payment'] = $orderPayment;
                $this->InfoData['comments'] = $orderComments;

                /** @updatedBy Debashis S. Gopal. Field name changed from discountAmount to discount. */
                $this->InfoData['discount'] = $discount;
                $this->InfoData['origin'] = "website";
            }

            $orderInfoEvent = "erpconnect_forward_orderinfo";
            $this->eventManager->dispatch($orderInfoEvent, ['order' => $this]);
            return $this->InfoData;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
