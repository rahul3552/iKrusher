<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward;

/**
 * Class responsible for preparing customer data which will be added in order result to ERP
 */
class Customer
{
    public $customerRepo;

    /**
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    public $customer;

    /**
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepo
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepo
    ) {
        $this->customerRepo = $customerRepo;
    }

    /**
     * Method to get customer information from order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @createdBy Sravani Polu
     */
    public function getCustomerEntity($order)
    {
        $isGuestOrder = $order->getCustomerIsGuest();
        $customerData['email'] = $order->getCustomerEmail();
        try {
            if (!$isGuestOrder) {
                $customerData['sourceId'] = $order->getCustomerId();
                $this->customer = $this->customerRepo->create()->getById($order->getCustomerId());
                $customerData['targetCustomerId'] = $this->customer->getCustomAttribute('target_customer_id') !== null ?
                    $this->customer->getCustomAttribute('target_customer_id')->getValue() : null;
                //@Hrusikesh Added firstName and lastName for Registered user
                $customerData['firstName'] = $order->getCustomerFirstname();
                $customerData['lastName'] = $order->getCustomerLastname();
            } else {
                $customerData['firstName'] = $order->getBillingAddress()->getFirstname();
                $customerData['lastName'] = $order->getBillingAddress()->getLastname();
                $customerData['sourceId'] = null;
                $customerData['targetCustomerId'] = null;
                $customerData['isGuest'] = true;
                $date = date_create($order->getCreatedAt());
                $customerData['createdAt'] = date_format($date, 'Y-m-d');
                $customerData['updatedAt'] = date_format($date, 'Y-m-d');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return $customerData;
    }
}
