<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed Magento REST API calls and the logic converted into interfaces.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer;

use \Magento\Store\Model\ScopeInterface;

/**
 * Class for creating customer in Magento
 */
class Create
{

    const TARGETID = "targetId";
    const EMAIL = "email";
    const SKIPOBS = "i95_observer_skip";
    const SAVINGSOURCE = "savingSource";
    public $logger;
    public $dataHelper;
    public $eventManager;
    public $validate;
    public $validateFields = [
        self::TARGETID=>'i95dev_cust_003',
        'firstName'=>'i95dev_cust_005',
        'lastName'=>'i95dev_cust_006',
        self::EMAIL=>'i95dev_cust_004',
        ];
    public $targetFieldErp = self::TARGETID;
    public $storeManager;
    public $stringData;
    public $entityCode;
    public $component;
    public $abstractDataPersistence;
    public $result;
    public $customerRepository;
    public $searchCriteriaBuilder;
    public $customerInterfaceFactory;
    public $customerInterface;
    public $genericHelper;

    public $groupRepository;
    public $groupInterface;
    public $isNewCustomer;
    public $customerFactory;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\GroupRepositoryInterfaceFactory $groupRepository
     * @param \Magento\Customer\Api\Data\GroupInterfaceFactory $groupInterface
     * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\GroupRepositoryInterfaceFactory $groupRepository,
        \Magento\Customer\Api\Data\GroupInterfaceFactory $groupInterface,
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->validate = $validate;
        $this->eventManager = $eventManager;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->groupInterface = $groupInterface;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->genericHelper = $genericHelper;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Create customer.
     *
     * @param array $stringData
     * @param string $entityCode
     * @param string $erp
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @updatedBy Divya Koona. Removed Magento REST API call and converted to interfaces.
     */
    public function createCustomer($stringData, $entityCode, $erp = null)
    {
         
        $this->stringData = $this->getEntityData($stringData);
        $this->entityCode = $entityCode;
        try {
            $this->validate->validateFields = $this->validateFields;
            $this->component = $this->dataHelper->getscopeConfig(
                'i95dev_messagequeue/I95DevConnect_settings/component',
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $this->validate->validateData($this->stringData);
                $customerEmail = $this->dataHelper->getValueFromArray(self::EMAIL, $this->stringData);

                $targetCustomerId = $this->dataHelper->getValueFromArray($this->targetFieldErp, $this->stringData);
                $customerInfo = $this->genericHelper->getCustomerInfoByTargetId($targetCustomerId);
            if (empty($customerInfo)) {
                if ($this->customerIsEmailAvailable($customerEmail)) {
                    $this->isNewCustomer = true;
                    $this->customerInterface = $this->customerInterfaceFactory->create();
                    $this->customerInterface->setCustomAttribute('target_customer_id', $targetCustomerId);
                    $this->customerInterface->setCustomAttribute('origin', $this->component);
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('i95dev_cust_009')
                    );
                }
            } else {
                $this->customerInterface = $this->customerInterfaceFactory->create();
                if (isset($customerInfo[0])) {
                    $this->customerInterface->setId($customerInfo[0]->getId());
                }
            }

                $this->prepareDataForApi();
                $beforeeventname = 'erpconnect_messagequeuetomagento_beforesave_' . $entityCode;
                $this->eventManager->dispatch($beforeeventname, ['currentObject' => $this]);
                $this->dataHelper->unsetGlobalValue(self::SKIPOBS);
                $this->dataHelper->setGlobalValue(self::SKIPOBS, true);

                $this->result = $this->customerRepository->create()->save($this->customerInterface);

            if (!is_object($this->result)) {
                $customerId = null;
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('i95dev_cust_010')
                );
            } else {
                $customerId = $this->result->getId();
                if ($this->isNewCustomer) {
                    $customer = $this->customerFactory->create()->load($customerId);
                    $customer->sendNewAccountEmail();
                }
            }
                $this->dataHelper->unsetGlobalValue(self::SKIPOBS);

                $jsondata = json_encode(["entityCode" => $entityCode,
                self::TARGETID => $this->dataHelper->getValueFromArray($this->targetFieldErp, $this->stringData),
                "source" => $erp]);

                $this->dataHelper->coreRegistry->unregister(self::SAVINGSOURCE);
                $this->dataHelper->coreRegistry->register(self::SAVINGSOURCE, $jsondata);

                $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_' . $entityCode;
                $this->eventManager->dispatch($aftereventname, ['currentObject' => $this]);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
        //Updated By Hrusikesh Manna To Return The Exception Instead Of Throughing
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }

        return $this->setCustomerResponse($customerId);
    }

    /**
     * @param $customerId
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function setCustomerResponse($customerId)
    {
        if (isset($customerId)) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                __("i95dev_cust_011"),
                $this->result->getId()
            );
        } else {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __('i95dev_cust_010'),
                null
            );
        }
    }

    /**
     * Prepare customer data interface
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona
     */
    public function prepareDataForApi()
    {
        $this->customerInterface->setEmail($this->dataHelper->getValueFromArray(self::EMAIL, $this->stringData));
        $this->customerInterface->setFirstname($this->dataHelper->getValueFromArray("firstName", $this->stringData));
        $this->customerInterface->setLastname($this->dataHelper->getValueFromArray("lastName", $this->stringData));

        $this->customerInterface->setPrefix($this->dataHelper->getValueFromArray("prefix", $this->stringData));
        $this->customerInterface->setSuffix($this->dataHelper->getValueFromArray("suffix", $this->stringData));
        $this->customerInterface->setMiddlename($this->dataHelper->getValueFromArray("middleName", $this->stringData));

        $this->customerInterface->setWebsiteId($this->storeManager->getDefaultStoreView()->getWebsiteId());
        $this->customerInterface->setStoreId($this->storeManager->getDefaultStoreView()->getStoreId());

        $customerGroup = $this->dataHelper->getValueFromArray("customerGroup", $this->stringData);

        if ($customerGroup) {
            $group = $this->getCustomerGroupInfo($customerGroup);
            if (empty($group)) {
                $customerGroupData = $this->groupInterface->create();
                $customerGroupData->setCode($customerGroup);
                $customerGroupData = $this->groupRepository->create()->save($customerGroupData);
                $groupId = $customerGroupData->getId();
            } else {
                $groupId = $group[0]->getId();
            }
        } else {
            $groupId = $this->dataHelper->getscopeConfig(
                'i95dev_messagequeue/I95DevConnect_settings/customer_group',
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
        }

        $this->customerInterface->setGroupId($groupId);

        $jsondata = json_encode(["entityCode" => $this->entityCode,
            self::TARGETID => $this->dataHelper->getValueFromArray($this->targetFieldErp, $this->stringData),
            "source" => "ERP"]);
        $this->dataHelper->coreRegistry->unregister(self::SAVINGSOURCE);
        $this->dataHelper->coreRegistry->register(self::SAVINGSOURCE, $jsondata);

        $this->customerInterface->setCustomAttribute('update_by', __('ERP'));
    }

    /**
     * Fetch Customer group based on customer group code
     *
     * @param string $customerGroupCode
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona. Removed Magento REST API call.
     */
    public function getCustomerGroupInfo($customerGroupCode)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                                   ->addFilter('customer_group_code', $customerGroupCode, 'eq')
                                   ->create();
            $searchResults = $this->groupRepository->create()->getList($searchCriteria);
            return $searchResults->getItems();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                \I95DevConnect\MessageQueue\Api\LoggerInterface::CRITICAL
            );
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Check if customer email is available in Magento or not
     *
     * @param string $email
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona. Removed Magento REST API call.
     */
    public function customerIsEmailAvailable($email)
    {
        try {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $this->customerRepository->create()->get($email, $websiteId);
            return false;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            return true;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
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
