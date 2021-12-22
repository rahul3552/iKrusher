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
class BillingAddress extends AbstractOrder
{

    const I95EXC = 'i95devApiException';
    const FIRSTNAME = "firstName";
    const LASTNAME = "lastName";
    const COUNTRYID = "countryId";
    const STREET = "street";
    const POSTCODE = "postcode";
    const TELEPHONE = "telephone";
    const REGIONID = "regionId";

    public $billingAddress;
    public $customerAddressModel;
    public $existingAddress = 0;
    public $dataHelper;
    public $regionDirectory;
    public $regionId;
    public $region;
    public $countryId;
    public $validateFields = [
        self::FIRSTNAME=>'i95dev_addr_002',
        self::LASTNAME=>'i95dev_addr_003',
        self::COUNTRYID=>'i95dev_addr_004',
        'city'=>'i95dev_addr_006',
        self::STREET=>'i95dev_addr_007',
        self::POSTCODE=>'i95dev_addr_008',
        self::TELEPHONE=>'i95dev_addr_009',
    ];
    public $storeManager;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \Magento\Customer\Model\AddressFactory $customerAddressModel
     * @param \Magento\Directory\Model\RegionFactory $regionDirectory
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \Magento\Customer\Model\AddressFactory $customerAddressModel,
        \Magento\Directory\Model\RegionFactory $regionDirectory,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->customerAddressModel = $customerAddressModel;
        $this->dataHelper = $dataHelper;
        $this->regionDirectory = $regionDirectory;
        $this->storeManager = $storeManager;

        parent::__construct(
            $logger,
            $genericHelper,
            $validate
        );
    }

    /**
     * Validate request billing address data
     *
     * @param array $stringData
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona. Added region address validation logic.
     */
    public function validateData($stringData)
    {
        $this->stringData = $stringData;
        $regionDetails = [];
        $this->billingAddress = $this->dataHelper->getValueFromArray("billingAddress", $this->stringData);
        $targetBillingAddressId = $this->dataHelper->getValueFromArray("targetId", $this->billingAddress);
        $component = $this->dataHelper->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
        if ($component == 'AX' || $component == 'D365FO') {
            if ($targetBillingAddressId == '') {
                throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_addr_001'));
            }
            $this->billingAddress = $this->genericHelper->getAddressByTargetAddressId(
                $targetBillingAddressId,
                $this->currentObject->customer->getId()
            );
            $regionDetails['region_id'] = $this->billingAddress['region_id'];
            $regionDetails['default_name'] = $this->billingAddress['region'];
            $this->countryId = $this->billingAddress['country_id'];
        } else {
            $this->validate->validateFields = $this->validateFields;
            $this->validate->validateData($this->billingAddress);
            $this->countryId = $this->billingAddress[self::COUNTRYID];
            $regionDetails = $this->dataHelper->getRegionDetails(
                $this->billingAddress[self::REGIONID],
                $this->billingAddress[self::COUNTRYID]
            );
        }

        if (!empty($regionDetails)) {
            $this->regionId = isset($regionDetails[self::REGIONID]) ?
                $regionDetails[self::REGIONID] : $regionDetails['region_id'];
            $this->region = $regionDetails['default_name'];
        } else {
            $stateRequiredCountries = $this->dataHelper->getscopeConfig(
                'general/region/state_required',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );
            $countriesList = explode(',', $stateRequiredCountries);
            if (in_array($this->billingAddress[self::COUNTRYID], $countriesList)) {
                $message =($this->billingAddress[self::REGIONID] == '') ? __('i95dev_addr_005') : __('i95dev_addr_014');

                $this->logger->create()->createLog(__METHOD__, $message, self::I95EXC, 'critical');
                throw new \Magento\Framework\Exception\LocalizedException(__($message));
            } else {
                $regionCode = $this->getRegionCode();
            }

            $this->regionId = 0;
            $this->region = $regionCode;
        }
    }

    /**
     * @return string
     */
    public function getRegionCode()
    {
        $regionList = $this->regionDirectory->create()->getCollection()
            ->addFieldToFilter("country_id", $this->billingAddress[self::COUNTRYID]);
        $regionList->getSelect()->limit(1);

        $regionList = $regionList->getData();
        return (!empty($regionList)) ? "" : $this->billingAddress[self::REGIONID];
    }

    /**
     * Prepare billing address which to be added in quote.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona. Removed region address validation logic.
     */
    public function addBillingAddress()
    {
        try {
            $firstname = isset($this->billingAddress[self::FIRSTNAME]) ?
                    $this->billingAddress[self::FIRSTNAME] : $this->billingAddress['firstname'];
            $lastname = isset($this->billingAddress[self::LASTNAME]) ?
                    $this->billingAddress[self::LASTNAME] : $this->billingAddress['lastname'];
            $customer_address_id = isset($this->billingAddress['entity_id']) ?
                    $this->billingAddress['entity_id'] : null;
            return [
                'address_type' => 'billing',
                'city' => $this->billingAddress['city'],
                'country_id' => $this->countryId,
                'email' => $this->currentObject->customer->getEmail(),
                'firstname' => $firstname,
                'lastname' => $lastname,
                self::POSTCODE => $this->billingAddress[self::POSTCODE],
                'region_id' => $this->regionId,
                'region' => $this->region,
                'customer_address_id' => $customer_address_id,
                self::STREET => [
                    0 => $this->billingAddress[self::STREET],
                    1 => (array_key_exists('street2', $this->billingAddress)) ? $this->billingAddress['street2'] : '',
                ],
                self::TELEPHONE => $this->billingAddress[self::TELEPHONE],
            ];
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
