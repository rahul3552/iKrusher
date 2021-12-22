<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Reverse;

use \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\AbstractOrder;

/**
 * Class for preparing shipping address while creating order
 */
class ShippingAddress extends AbstractOrder
{

    const I95EXC = 'i95devApiException';
    const COUNTRYID = "countryId";
    const REGIONID = "regionId";
    const CRICTICAL = "critical";

    public $shippingAddress;
    public $customerAddressModel;
    public $shipconfig;
    public $regionDirectory;
    public $regionId;
    public $region;
    public $validateFields = [
        'firstName'=>'i95dev_addr_002',
        'lastName'=>'i95dev_addr_003',
        self::COUNTRYID=>'i95dev_addr_004',
        'city'=>'i95dev_addr_006',
        'street'=>'i95dev_addr_007',
        'postcode'=>'i95dev_addr_008',
        'telephone'=>'i95dev_addr_009'
    ];

    public $dataHelper;

    /**
     * var  \Magento\Quote\Api\Data\AddressInterface
     */
    public $quoteshippingAddressInterface;
    public $scopeConfig;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Customer\Model\AddressFactory $customerAddressModel
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \Magento\Shipping\Model\Config $shipconfig
     * @param \Magento\Directory\Model\RegionFactory $regionDirectory
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteshippingAddressInterface
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Customer\Model\AddressFactory $customerAddressModel,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \Magento\Shipping\Model\Config $shipconfig,
        \Magento\Directory\Model\RegionFactory $regionDirectory,
        \Magento\Quote\Api\Data\AddressInterface $quoteshippingAddressInterface,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->dataHelper = $dataHelper;
        $this->customerAddressModel = $customerAddressModel;
        $this->shipconfig = $shipconfig;
        $this->regionDirectory = $regionDirectory;
        $this->quoteshippingAddressInterface = $quoteshippingAddressInterface;
        $this->scopeConfig = $scopeConfig;

        parent::__construct(
            $logger,
            $genericHelper,
            $validate
        );
    }

    /**
     * Validate request shipping information data
     *
     * @param array $stringData
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona. Removed shipping method validation from shipping address and validated from header
     */
    public function validateData($stringData)
    {
        $this->stringData = $stringData;
        $this->shippingAddress = $this->dataHelper->getValueFromArray("shippingAddress", $this->stringData);
        $this->validate->validateFields = $this->validateFields;
        $this->validate->validateData($this->shippingAddress);
        $regionDetails = $this->dataHelper->getRegionDetails(
            $this->shippingAddress[self::REGIONID],
            $this->shippingAddress[self::COUNTRYID]
        );

        if (!empty($regionDetails)) {
            $this->regionId = $regionDetails['region_id'];
            $this->region = $regionDetails['default_name'];
        } else {
            $stateRequiredCountries = $this->dataHelper->getscopeConfig(
                'general/region/state_required',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );
            $countriesList = explode(',', $stateRequiredCountries);
            if (in_array($this->shippingAddress[self::COUNTRYID], $countriesList)) {
                $message = ($this->shippingAddress[self::REGIONID] == '') ?
                    __('i95dev_addr_005') : __('i95dev_addr_014');

                $this->logger->create()->createLog(__METHOD__, $message, self::I95EXC, self::CRICTICAL);
                throw new \Magento\Framework\Exception\LocalizedException(__($message));
            } else {
                $regionList = $this->regionDirectory->create()->getCollection()
                    ->addFieldToFilter('country_id', $this->shippingAddress[self::COUNTRYID]);

                $regionList->getSelect()->limit(1);

                $regionList  = $regionList->getData();
                $regionCode = (!empty($regionList)) ? "" : $this->shippingAddress[self::REGIONID];
            }

            $this->regionId = 0;
            $this->region = $regionCode;
        }

        $activeMethods = $this->getActiveShippingMethods();
        if (!empty($activeMethods)) {
            if (!in_array($this->dataHelper->getValueFromArray("shippingMethod", $stringData), $activeMethods)) {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_021"));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_quote_all_shippingMethod_active"));
        }
    }

    /**
     * Prepare Quote Address
     *
     * @return \Magento\Quote\Api\Data\AddressInterface $address
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona. Removed region validation logic
     */
    public function addShippingAddress()
    {
        try {
            $street2 = (array_key_exists('street2', $this->shippingAddress)) ? $this->shippingAddress['street2'] : '';
            $street = [$this->shippingAddress['street'],$street2];
            $address = $this->quoteshippingAddressInterface;
            $address->setCustomerId($this->currentObject->customer->getId());
            $address->setFirstname($this->shippingAddress['firstName']);
            $address->setLastname($this->shippingAddress['lastName']);
            $address->setEmail($this->currentObject->customer->getEmail());
            $address->setRegionId($this->regionId);
            $address->setRegion($this->region);
            $address->setPostcode($this->shippingAddress['postcode']);
            $address->setStreet($street);
            $address->setCity($this->shippingAddress['city']);
            $address->setTelephone($this->shippingAddress['telephone']);
            $address->setCountryId($this->shippingAddress[self::COUNTRYID]);
            $address->setAddressType("shipping");
            return $address;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, self::CRICTICAL);
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Get Active Shipping Methods
     *
     * @return array $methods
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona
     */
    public function getActiveShippingMethods()
    {
        $methods = [];
        try {
            $activeCarriers = $this->shipconfig->getActiveCarriers();
            foreach ($activeCarriers as $carrierCode => $carrierModel) {
                if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                    foreach ($carrierMethods as $methodCode => $method) {
                        $code = $carrierCode . '_' . $methodCode;
                        $methods[] = $code;
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, self::CRICTICAL);
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return $methods;
    }
}
