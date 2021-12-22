<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed Magento REST API calls and converted to interface code.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address;

/**
 * Class for creating customer address in magento
 */
class Create
{
    const VALUE='value';
    const COUNTRY_ID='country_id';
    const COUNTRYID='countryId';
    const TELEPHONE='telephone';
    const CRITICAL='critical';
    const REGIONID='regionId';

    public $requestHelper;
    public $dataHelper;
    public $eventManager;
    public $validate;
    public $logger;
    public $abstractDataPersistence;
    const I95EXC = 'i95devApiException';
    private $regionDetails;
    public $postData;
    public $resutlData;
    public $stringData;
    public $customerId = 0;
    public $targetAddressId = 0;
    public $responseId = '';
    public $targetFieldErp = "targetId";
    public $validateFields = [
        'targetId' => 'i95dev_addr_001',
        'firstName' => 'i95dev_addr_002',
        'lastName' => 'i95dev_addr_003',
        self::COUNTRYID => 'i95dev_addr_004',
        'city' => 'i95dev_addr_006',
        'street' => 'i95dev_addr_007',
        'postcode' => 'i95dev_addr_008',
        'telephone' => 'i95dev_addr_009'
    ];
    public $regionDirectory;
    public $scopeConfig;
    public $customerRepository;
    public $searchCriteriaBuilder;
    public $addressRepository;
    public $addressInterfaceFactory;
    public $addressInterface;
    public $existingAddress;
    public $regionInterfaceFactory;
    public $regionInterface;
    public $storeManager;
    public $addressResponse;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     * @param \Magento\Directory\Model\RegionFactory $regionDirectory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressInterfaceFactory
     * @param \Magento\Customer\Api\Data\RegionInterfaceFactory $regionInterfaceFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence,
        \Magento\Directory\Model\RegionFactory $regionDirectory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressInterfaceFactory,
        \Magento\Customer\Api\Data\RegionInterfaceFactory $regionInterfaceFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->requestHelper = $requestHelper;
        $this->dataHelper = $dataHelper;
        $this->eventManager = $eventManager;
        $this->validate = $validate;
        $this->logger = $logger;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->regionDirectory = $regionDirectory;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->addressRepository = $addressRepository;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
        $this->regionInterfaceFactory = $regionInterfaceFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Create customer address.
     *
     * @param array $stringData
     * @param string $entityCode
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona
     */
    public function createAddress($stringData, $entityCode)
    {

        $this->stringData = $this->getEntityData($stringData);

        try {
            $this->validate->validateFields = $this->validateFields;

            if ($this->validate->validateData($this->stringData)) {
                $this->validateData();
                $targetCustomerId = $this->dataHelper->getValueFromArray("targetCustomerId", $this->stringData);
                $this->targetAddressId = $this->dataHelper->getValueFromArray($this->targetFieldErp, $this->stringData);
                if ($this->getExistingCustomerData($targetCustomerId)) {
                    $this->addAddressData();
                    /* updatedBy Ranjith R, added the before save event dispatch */
                    $beforeeventname = 'erpconnect_messagequeuetomagento_beforesave_' . $entityCode;
                    $this->eventManager->dispatch($beforeeventname, ['currentObject' => $this]);
                    $this->dataHelper->unsetGlobalValue('i95_observer_skip');
                    $this->dataHelper->setGlobalValue('i95_observer_skip', true);
                    $result = $this->addressRepository->create()->save($this->addressInterface);
                    $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_' . $entityCode;
                    $this->eventManager->dispatch($aftereventname, ['currentObject' => $this]);
                    if (is_object($result)) {
                        $this->responseId = $result->getId();
                        return $this->abstractDataPersistence->setResponse(
                            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                            __("i95dev_addr_017"),
                            $this->responseId
                        );
                    } else {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('i95dev_addr_018')
                        );
                    }
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_addr_019'));
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     * Set ERP responses in address
     *
     * @param type $requestData
     * @param type $entityCode
     * @param type $erpCode
     */
    public function setAddressResponse($requestData, $entityCode, $erpCode = null)
    {
        $this->addressResponse->setAddressResponse($requestData, $entityCode, $erpCode);
    }
    /**
     * Search the customer based on targetAddressId. If not found throw 'Customer not exists ' error.
     *
     * @param string $targetCustomerId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona
     */
    public function getExistingCustomerData($targetCustomerId)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                                   ->addFilter('target_customer_id', $targetCustomerId, 'eq')
                                   ->create();
            $searchResults = $this->customerRepository->create()->getList($searchCriteria);
            $customerInfo = $searchResults->getItems();
            if (!empty($customerInfo)) {
                if (isset($customerInfo[0])) {
                    $customerData = $customerInfo[0];
                    $this->customerId = $customerData->getId();
                    $this->postData = ['customer' => $customerData];
                } else {
                    return false;
                }
                return true;
            } else {
                return false;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, self::CRITICAL);
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Identifies new/existing address
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona
     */
    public function addAddressData()
    {
        if ($this->isNewAddress()) {
            $this->addNewAddress();
        } else {
            $this->updateExistingAddress();
        }
    }

    /**
     * Check whether the ERP given address is a new address r not.
     * It it is an existing address that address will be updated.
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona
     */
    public function isNewAddress()
    {
        try {
            foreach ($this->postData['customer']->getAddresses() as $address) {
                if (!empty($address->getCustomAttributes())) {
                    foreach ($address->getCustomAttributes() as $addressCusttomAttr) {
                        if (($addressCusttomAttr->getAttributeCode() == 'target_address_id') &&
                            ($addressCusttomAttr->getValue() == $this->targetAddressId)) {
                            $this->existingAddress = $address;
                            return false;
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, self::CRITICAL);
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return true;
    }

    /**
     * Update changes in the existing address of the customer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona
     */
    public function updateExistingAddress()
    {
        try {
            $this->addressInterface = $this->addressInterfaceFactory->create();
            $this->addressInterface->setId($this->existingAddress->getId());
            $this->prepareAddressInterface();
            $this->responseId = $this->existingAddress->getId();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, self::CRITICAL);
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Prepares address interface with the provided data
     * @createdBy Divya Koona
     */
    public function prepareAddressInterface()
    {
        $erpRegionId = $this->dataHelper->getValueFromArray(self::REGIONID, $this->stringData);
        $countryId = $this->dataHelper->getValueFromArray(self::COUNTRYID, $this->stringData);
        $this->regionDetails = $this->getRegionDetails($erpRegionId, $countryId);

        $this->addressInterface->setCustomerId($this->customerId);
        $this->addressInterface->setPrefix($this->dataHelper->getValueFromArray("prefix", $this->stringData));
        $this->addressInterface->setSuffix($this->dataHelper->getValueFromArray("suffix", $this->stringData));
        $this->addressInterface->setMiddlename($this->dataHelper->getValueFromArray("middleName", $this->stringData));
        $this->addressInterface->setFirstname($this->dataHelper->getValueFromArray("firstName", $this->stringData));
        $this->addressInterface->setLastname($this->dataHelper->getValueFromArray("lastName", $this->stringData));
        $this->addressInterface->setCity($this->dataHelper->getValueFromArray("city", $this->stringData));
        $this->addressInterface->setTelephone($this->dataHelper->getValueFromArray(self::TELEPHONE, $this->stringData));
        $this->addressInterface->setPostcode($this->dataHelper->getValueFromArray("postcode", $this->stringData));

        $street = [];
        $street[0] = $this->dataHelper->getValueFromArray("street", $this->stringData);
        $street2 = $this->dataHelper->getValueFromArray("street2", $this->stringData);
        $street[1] = isset($street2) ? $street2 : '';

        $this->addressInterface->setStreet($street);

        $this->regionInterface = $this->regionInterfaceFactory->create();
        if (!empty($this->regionDetails)) {
            $regionId = $this->dataHelper->getValueFromArray("region_id", $this->regionDetails);
            $this->regionInterface->setRegionCode($this->dataHelper->getValueFromArray("code", $this->regionDetails));
            $this->regionInterface->setRegionId($regionId);
            $this->regionInterface->setRegion($this->dataHelper->getValueFromArray("name", $this->regionDetails));
            $this->addressInterface->setRegion($this->regionInterface);
            $this->addressInterface->setRegionId($regionId);
        } else {
            $regionList = $this->regionDirectory->create()->getCollection()
                ->addFieldToFilter(self::COUNTRY_ID, $countryId);
            $regionList->getSelect()->limit(1);
            $regionList = $regionList->getData();
            if (!empty($regionList)) {
                $regionCode = "";
            } else {
                $regionCode = $this->dataHelper->getValueFromArray(self::REGIONID, $this->stringData);
            }

            $this->regionInterface->setRegionCode($regionCode);
            $this->regionInterface->setRegionId(0);
            $this->regionInterface->setRegion($regionCode);
            $this->addressInterface->setRegion($this->regionInterface);
            $this->addressInterface->setRegionId(0);
        }

        $this->addressInterface->setCountryId($countryId);
        if (trim($this->dataHelper->getValueFromArray("isDefaultBilling", $this->stringData))) {
            $this->addressInterface->setIsDefaultBilling(true);
        }
        if (trim($this->dataHelper->getValueFromArray("isDefaultShipping", $this->stringData))) {
            $this->addressInterface->setIsDefaultShipping(true);
        }
    }

    /**
     * Add new address to existing customer.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona
     */
    public function addNewAddress()
    {
        try {
            $this->addressInterface = $this->addressInterfaceFactory->create();
            $this->prepareAddressInterface();
            $this->addressInterface->setCustomAttribute(
                'target_address_id',
                $this->dataHelper->getValueFromArray($this->targetFieldErp, $this->stringData)
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, self::CRITICAL);
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Returns region details based on given region code and country code
     *
     * @param string $regionCode
     * @param string $countryCode
     * @return array
     */
    public function getRegionDetails($regionCode, $countryCode)
    {
        return $this->dataHelper->getRegionDetails($regionCode, $countryCode);
    }

    /**
     * Validates address data from ERP
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData()
    {
        $msg = [];
        $countryId = $this->dataHelper->getValueFromArray(self::COUNTRYID, $this->stringData);
        $regionId = $this->dataHelper->getValueFromArray(self::REGIONID, $this->stringData);
        $telephoneNumber = $this->dataHelper->getValueFromArray(self::TELEPHONE, $this->stringData);
        $stateRequiredCountries = $this->scopeConfig->getValue(
            'general/region/state_required',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        $countriesList = explode(',', $stateRequiredCountries);
        if (in_array($countryId, $countriesList)) {
            if ($regionId == '') {
                $msg[] = __('i95dev_addr_005');
            } else {
                $regionList = $this->regionDirectory->create()->getCollection()
                ->addFieldToFilter(self::COUNTRY_ID, $countryId);
                $regionList->getSelect()->limit(1);
                $regionList = $regionList->getData();

                if (!empty($regionList)) {
                    $region = $this->regionDirectory->create()->getCollection()
                    ->addFieldToFilter(self::COUNTRY_ID, $countryId)
                    ->addFieldToFilter('code', $regionId);
                    $region->getSelect()->limit(1);

                    $region = $region->getData();
                    if (empty($region)) {
                        $msg[] = __('i95dev_addr_014');
                    }
                }
            }
        }

        if (empty($telephoneNumber)) {
            $msg[] = __('i95dev_addr_009');
        }

        if (!empty($msg)) {
            $message = implode(', ', $msg);
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        return true;
    }

    /**
     * Get the string data
     *
     * @param string $stringData
     * @createdBy Ranjith R. to support plugins
     * @return string
     * @return string
     */
    public function getEntityData($stringData)
    {
        return $stringData;
    }
}
