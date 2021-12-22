<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer;

/**
 * Class for Create customer group, get customer group info and set customer group response
 */
class CustomerGroup
{
    const I95EXC = 'i95devApiException';
    public $create;
    public $customerGroupResponse;
    public $customerGroup;
    public $customerGroupInfo;
    public $logger;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup\Create $create
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup\Info $customerGroupInfo
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup\Response $customerGroupResponse
     * @param \I95DevConnect\MessageQueue\Model\CustomerGroup $customerGroup
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup\Create $create,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup\Info $customerGroupInfo,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup\Response $customerGroupResponse,
        \I95DevConnect\MessageQueue\Model\CustomerGroup $customerGroup,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
    ) {

        $this->create = $create;
        $this->customerGroupInfo = $customerGroupInfo;
        $this->customerGroupResponse = $customerGroupResponse;
        $this->customerGroup = $customerGroup;
        $this->logger = $logger;
    }

    /**
     * Create customer group.
     *
     * @param string $stringData
     * @param string $entityCode
     * @param string $erp
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Exception
     * @throws \Exception
     */
    public function create($stringData, $entityCode, $erp)
    {
        return $this->create->createCustomerGroup($stringData, $entityCode, $erp);
    }

    /**
     * Get customer group information
     *
     * @param int $customerGroupId
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInfo($customerGroupId, $entityCode, $erpCode)
    {
        return  $this->customerGroupInfo->getInfo($customerGroupId, $entityCode, $erpCode);
    }

    /**
     * Sets target customer group information
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function getResponse($requestData, $entityCode, $erpCode)
    {
        return $this->customerGroupResponse->getResponse($requestData, $entityCode, $erpCode);
    }

    /**
     * Get customer group code by group id
     *
     * @param int $customerGroupId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGroupEntityByGroupId($customerGroupId)
    {
        $customerGroupEntity = $this->customerGroupInfo->getCustomerGroupById($customerGroupId);
        return $customerGroupEntity->getCode();
    }

    /**
     * Fetch customer group by customer group id
     *
     * @param int $sourceCustomerGroupId
     * @return array
     */
    public function getCustomerGroupById($sourceCustomerGroupId)
    {

        try {
            $customGroupModel = $this->customerGroup;
            $customGroupCollection = $customGroupModel->getCollection()
                            ->addFieldToFilter('customer_group_id', $sourceCustomerGroupId);
            $customGroupCollection->getSelect()->limit(1);

            $customGroupCollection = $customGroupCollection->getData();
            $customCustomerGroupId = isset($customGroupCollection[0]['id']) ? $customGroupCollection[0]['id'] : '';
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
        return $customCustomerGroupId;
    }
}
