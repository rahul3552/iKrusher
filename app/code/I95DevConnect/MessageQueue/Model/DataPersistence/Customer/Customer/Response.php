<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed getCustomerById function and added in Generic Helper.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer;

/**
 * Class responsible for saving erp responses in customer
 */
class Response
{

    public $dataHelper;
    public $eventManager;
    public $i95DevMagentoMQRepository;
    public $i95DevMagentoMQData;
    public $customerId;
    public $erpCode;
    public $targetId;
    public $entityCode;
    public $address;
    public $storeManager;
    public $stringData;
    public $customerRepository;
    public $customerInterfaceFactory;
    public $genericHelper;
    public $customerInterface;
    public $abstractDataPersistence;
    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterface
     */
    public $logger;
    const SKIPOBRVR = "i95_observer_skip";

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address $address
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address $address,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerInterfaceFactory,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->dataHelper = $dataHelper;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->i95DevMagentoMQData = $i95DevMagentoMQData;
        $this->eventManager = $eventManager;
        $this->address = $address;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->genericHelper = $genericHelper;
        $this->logger = $logger;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     * Sets target customer details in customer
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface author Divya Koona.
     * Removed Magento REST API calls and converted to interface code.
     * author Divya Koona. Removed Magento REST API calls and converted to interface code.
     */
    public function getResponse($requestData, $entityCode, $erpCode)
    {
        try {
            $this->stringData = $requestData;
            $this->customerId = $this->dataHelper->getValueFromArray("sourceId", $requestData);
            $this->targetId = $this->dataHelper->getValueFromArray("targetId", $requestData);
            $this->entityCode = $entityCode;
            $inputData = [];
            $customer = $this->genericHelper->getCustomerById($this->customerId);
            if (isset($customer['id'])) {
                $this->erpCode = isset($erpCode) ? $erpCode : __("ERP");

                $customerDataInterface = $this->prepareCustomerDataInterface($customer);
                $this->dataHelper->unsetGlobalValue(self::SKIPOBRVR);
                $this->dataHelper->setGlobalValue(self::SKIPOBRVR, true);
                $result = $this->customerRepository->create()->save($customerDataInterface);
                $addresses = null;
                if (isset($requestData['inputData'])) {
                    $addresses = $this->dataHelper->getValueFromArray("addresses", $requestData['inputData']);
                }

                if (!empty($addresses)) {
                    $addressRes = $this->getAddressResponse($addresses, $customer, $erpCode);
                    if (!empty($addressRes)) {
                        $inputData[] = $addressRes;
                    }
                }

                $customerResponseEvent = "erpconnect_forward_customerresponse";
                $this->eventManager->dispatch($customerResponseEvent, ['currentObject' => $this]);
                $this->dataHelper->unsetGlobalValue(self::SKIPOBRVR);

                if (is_object($result)) {
                    return $this->abstractDataPersistence->setResponse(
                        \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                        __("Response send successfully"),
                        $inputData
                    );
                } else {
                    return $this->abstractDataPersistence->setResponse(
                        \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                        __("Some error occured in response sync"),
                        null
                    );
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            /** @author Hrusieksh Manna. Returning error message instead of throwing exception,
             * as expected by the calling function. **/
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
            $message = "Customer Not Found :: " . $this->customerId;
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($message),
                null
            );
        }
    }

    /**
     * @param $addresses
     * @param $customer
     * @param $erpCode
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddressResponse($addresses, $customer, $erpCode)
    {
        $inputData = [];

        foreach ($addresses as $addressResquest) {
            foreach ($customer['addresses'] as $address) {
                if (isset($address['id']) &&
                    $address['id'] == $this->dataHelper->getValueFromArray("sourceId", $addressResquest)) {
                    $addressReq = $addressResquest;
                    $addressReq['customer'] = $customer;
                    $addressResponse = $this->address->setAddressResponse($addressReq, "Address", $erpCode);
                    if ($addressResponse) {
                        $inputData = $addressResquest;
                    }
                }
            }
        }

        return $inputData;
    }
    /**
     * Prepare customer data interface
     *
     * @param array $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @createdBy Divya Koona
     */
    public function prepareCustomerDataInterface($customer)
    {
        $this->customerInterface = $this->customerInterfaceFactory->create();
        $this->customerInterface->setId($this->customerId);
        $this->customerInterface->setEmail($customer['email']);
        $this->customerInterface->setFirstname($customer['firstname']);
        $this->customerInterface->setLastname($customer['lastname']);
        $this->customerInterface->setWebsiteId($this->storeManager->getDefaultStoreView()->getWebsiteId());
        $this->customerInterface->setGroupId($customer['group_id']);
        $this->customerInterface->setCustomAttribute('target_customer_id', $this->targetId);
        $this->customerInterface->setCustomAttribute('update_by', $this->erpCode);
        foreach ($customer['custom_attributes'] as $attribute) {
            $attributeCode = $attribute['attribute_code'];
            $value = $attribute['value'];
            $this->customerInterface->setCustomAttribute($attributeCode, $value);
        }
        return $this->customerInterface;
    }
}
