<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed getCustomerAddressById function and added in Generic Helper.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address;

/**
 * Class responsible for saving erp responses in customer address
 */
class Response
{
    public $dataHelper;
    public $eventManager;
    public $i95DevMagentoMQRepository;
    public $i95DevMagentoMQData;
    public $targetAddressId;
    public $statusCode = '5';
    public $updatedBy = 'ERP';
    public $customer;
    public $erpCode;
    public $targetId;
    public $entityCode;
    public $address;
    public $addressId;
    public $targetCustomerId;
    public $addressRepository;
    public $addressInterfaceFactory;
    public $addressInterface;
    public $genericHelper;

    public $messageId;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressInterfaceFactory
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressInterfaceFactory,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->i95DevMagentoMQData = $i95DevMagentoMQData;
        $this->eventManager = $eventManager;
        $this->addressRepository = $addressRepository;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
        $this->genericHelper = $genericHelper;
    }

    /**
     * Sets target address details in customer address
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona. Removed Magento REST API calls and converted to interface code.
     */
    public function setAddressResponse($requestData, $entityCode, $erpCode = null)
    {
        $this->customer = $this->dataHelper->getValueFromArray("customer", $requestData);
        $this->addressId = $this->dataHelper->getValueFromArray("sourceId", $requestData);
        $this->targetAddressId = $this->dataHelper->getValueFromArray("targetId", $requestData);
        $this->targetCustomerId = $this->dataHelper->getValueFromArray("targetCustomerId", $requestData);
        $this->messageId = $this->dataHelper->getValueFromArray("messageId", $requestData);
        $this->entityCode = $entityCode;

        if ($address = $this->genericHelper->getCustomerAddressById($this->addressId)) {
            if (isset($erpCode)) {
                $this->erpCode = $erpCode;
            } else {
                $this->erpCode = __("ERP");
            }

            if (!empty($this->messageId)) {
                $this->saveDataInOutboundMQ($address);
            }

            $customerId = isset($this->customer['id']) ? $this->customer['id'] : null;
            $this->addressInterface = $this->addressInterfaceFactory->create();
            $this->addressInterface->setId($this->addressId);
            $this->addressInterface->setCustomerId($customerId);
            $this->addressInterface->setCustomAttribute('target_address_id', $this->targetAddressId);

            $this->addressRepository->create()->save($this->addressInterface);
            $addressResponseEvent = "erpconnect_forward_addressresponse";
            $this->eventManager->dispatch($addressResponseEvent, ['currentObject' => $this]);
            return true;
        }
    }
}
