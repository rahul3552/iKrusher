<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed getCustomerAddressById function and added in Generic Helper.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address;

/**
 * Class for preparing customer address result data to be sent to ERP
 */
class Info
{

    const STREET = "street";
    const VALUE = "value";
    public $dataHelper;
    public $addressData;
    public $eventManager;
    public $fieldMapInfo = [
                'sourceId' => 'id',
                'firstName' => 'firstname',
                'lastName' => 'lastname',
                'middleName' => 'middlename',
                'targetCustomerId' => 'targetCustomerId',
                'city' => 'city',
                'telephone' => 'telephone',
                'countryId' => 'country_id',
                'postcode' => 'postcode'
            ];
    public $genericHelper;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->eventManager = $eventManager;
        $this->genericHelper = $genericHelper;
    }

    /**
     * Prepare address information to be added in customer result
     *
     * @param string $addressId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddressInfo($addressId)
    {
        $this->addressData = [];
        if ($address = $this->genericHelper->getCustomerAddressById($addressId)) {
            $this->addressData = $this->dataHelper->prepareInfoArray($this->fieldMapInfo, $address);
            $this->prepareAddressDataArr($address);

            if (isset($address['custom_attributes'])) {
                foreach ($address['custom_attributes'] as $value) {
                    if ($value['attribute_code'] == 'target_address_id') {
                        $this->addressData['targetId'] = $value[self::VALUE];
                        $this->addressData['targetAddressId'] = $value[self::VALUE];
                        $this->addressData['reference'] = $value[self::VALUE];
                    }
                }
            }

            $addressInfoEvent = "erpconnect_forward_addressinfo";
            $this->eventManager->dispatch($addressInfoEvent, ['currentObject' => $address]);
            return $this->addressData;
        }
    }

    /**
     * @param $address
     */
    public function prepareAddressDataArr($address)
    {
        $this->addressData[self::STREET] = $address[self::STREET][0];
        if (isset($address[self::STREET])) {
            $street2 = isset($address[self::STREET][1]) ? $address[self::STREET][1] : '';
            $this->addressData['street2'] = $street2;
        } else {
            $this->addressData['street2'] = '';
        }

        if (isset($address['region']['region_code'])) {
            $this->addressData['regionId'] = $address['region']['region_code'];
        }
        if (isset($address['default_billing'])) {
            $this->addressData['isDefaultBilling'] = $address['default_billing'];
        } else {
            $this->addressData['isDefaultBilling'] = false;
        }
        if (isset($address['default_shipping'])) {
            $this->addressData['isDefaultShipping'] = $address['default_shipping'];
        } else {
            $this->addressData['isDefaultShipping'] = false;
        }
    }

    /**
     * Adds address information in customer result
     *
     * @param array $customer
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setAddressData($customer)
    {
        $addressData = [];
        if (isset($customer['addresses'])) {
            foreach ($customer['addresses'] as $address) {
                $addressData[] = $this->getAddressInfo($address['id']);
            }
        }
        return $addressData;
    }
}
