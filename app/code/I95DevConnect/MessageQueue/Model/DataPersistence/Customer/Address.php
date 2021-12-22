<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed getCustomerAddressById,getAddressInfoByObject functions as they are not used.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer;

/**
 * Class for Create customer address
 */
class Address
{

    const I95EXC = 'i95devApiException';

    public $customerCollection;
    public $customerAddressModel;
    public $requestHelper;
    public $postData;
    public $resutlData;
    public $customerId = 0;
    public $targetAddressId = 0;
    public $responseId = '';
    public $addressResponse;
    public $addressInfo;
    public $create;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address\Response $addressResponse
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address\Info $addressInfo
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address\Create $create
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address\Response $addressResponse,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address\Info $addressInfo,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address\Create $create
    ) {
        $this->requestHelper = $requestHelper;
        $this->addressResponse = $addressResponse;
        $this->addressInfo = $addressInfo;
        $this->create = $create;
    }

    /**
     * Create customer address.
     *
     * @param $stringData
     * @param $entityCode
     * @param $erp
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($stringData, $entityCode, $erp)
    {
        return $this->create->createAddress($stringData, $entityCode, $erp);
    }

    /**
     * Sets target address information
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setAddressResponse($requestData, $entityCode, $erpCode = null)
    {
        return $this->addressResponse->setAddressResponse($requestData, $entityCode, $erpCode);
    }

    /**
     * Get address from customer information
     *
     * @param array $customer
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInfo($customer)
    {
        return $this->addressInfo->setAddressData($customer);
    }
}
