<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward;

/**
 * Class responsible for preparing order's billing address data which will be added in order result to ERP
 * @createdBy Sravani Polu
 */
class BillingAddress
{
    public $addressRepo;

    /**
     *
     * @param \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepo
     */
    public function __construct(
        \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepo
    ) {
        $this->addressRepo = $addressRepo;
    }

    /**
     * method to prepare Order's Billing Address array
     * @param  \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     * @throws \Exception
     * @createdBy Sravani Polu
     */
    public function getBillingAddress($order)
    {
        try {
            $billingAddress = $order->getBillingAddress();
            if ($billingAddress !== null) {
                $orderBillingAddress['firstName'] = $billingAddress->getFirstname();
                $orderBillingAddress['middleName'] = $billingAddress->getMiddlename();
                $orderBillingAddress['lastName'] = $billingAddress->getLastname();
                $orderBillingAddress['postcode'] = $billingAddress->getPostcode();
                $orderBillingAddress['regionId'] = $billingAddress->getRegionCode();
                $orderBillingAddress['region'] = $billingAddress->getRegion();
                $orderBillingAddress['city'] = $billingAddress->getCity();
                $orderBillingAddress['countryId'] = $billingAddress->getCountryId();
                $orderBillingAddress['street'] = is_array($billingAddress->getStreet()) ?
                    $billingAddress->getStreet()[0] : null;
                $orderBillingAddress['street2'] = (array_key_exists(1, $billingAddress->getStreet())) ?
                    $billingAddress->getStreet()[1] : null;
                $orderBillingAddress['telephone'] = $billingAddress->getTelephone();
                $sourceAddressId = $billingAddress->getCustomerAddressId();
                $orderBillingAddress['sourceId'] = isset($sourceAddressId) ? $sourceAddressId : null;
                if ($sourceAddressId) {
                    $customerAddress = $this->addressRepo->create()->getById($sourceAddressId);
                    $orderBillingAddress['targetId'] =
                        ($customerAddress->getCustomAttribute('target_address_id') !== null) ?
                        $customerAddress->getCustomAttribute('target_address_id')->getValue() : null;
                } else {
                    $orderBillingAddress['targetId'] = null;
                }

                return $orderBillingAddress;
            } else {
                return null;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
