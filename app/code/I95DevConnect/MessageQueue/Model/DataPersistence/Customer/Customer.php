<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed getCustomerById function as it is not used.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer;

/**
 * Class for Create customer, get customerinfo, set customer response
 */
class Customer
{
    private $requestHelper;
    public $customerInfo;
    public $customerResponse;
    public $create;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer\Response $customerResponse
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer\Info $customerInfo
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer\Create $create
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer\Response $customerResponse,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer\Info $customerInfo,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer\Create $create
    ) {
        $this->requestHelper = $requestHelper;
        $this->customerResponse = $customerResponse;
        $this->customerInfo = $customerInfo;
        $this->create = $create;
    }

    /**
     * Create customer.
     *
     * @param string $stringData
     * @param string $entityCode
     * @param string $erp
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function create($stringData, $entityCode, $erp)
    {
        return $this->create->createCustomer($stringData, $entityCode, $erp);
    }

    /**
     * Get customer information
     *
     * @param int $customerId
     * @param string $entityCode
     * @param string $erpCode
     * @param int|null $messageId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInfo($customerId, $entityCode, $erpCode, $messageId)
    {
        return  $this->customerInfo->getInfo($customerId, $entityCode, $erpCode, $messageId);
    }

    /**
     * Sets target customer information
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function getResponse($requestData, $entityCode, $erpCode)
    {
        return $this->customerResponse->getResponse($requestData, $entityCode, $erpCode);
    }
}
