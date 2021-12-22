<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup;

use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;
use \Magento\Framework\Exception\LocalizedException;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Class responsible for saving erp responses in customer group
 */
class Response
{

    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterface
     */
    public $logger;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     *
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;

    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\CustomerGroupFactory
     */
    public $i95DevGroupFactory;

    /**
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    public $groupRepository;

    protected $customerGroupId;
    protected $targetCustomerGroup;
    protected $erpCode;
    protected $customerGroup = null;
    public $abstractDataPersistence;
    const CRITICAL = "critical";

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \I95DevConnect\MessageQueue\Model\CustomerGroupFactory $i95DevGroupFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Event\Manager $eventManager,
        LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \I95DevConnect\MessageQueue\Model\CustomerGroupFactory $i95DevGroupFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->dataHelper = $dataHelper;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->date = $date;
        $this->i95DevGroupFactory = $i95DevGroupFactory;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Sets target customer group details.
     *
     * @param array $requestData
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @author Debashis S. Gopal. Code changed from api to repository interface to save target details
     */
    public function getResponse($requestData)
    {
        $this->customerGroupId = $this->dataHelper->getValueFromArray("sourceId", $requestData);
        $this->targetCustomerGroup = $this->dataHelper->getValueFromArray("targetId", $requestData);
        try {
            if ($this->validateData()) {
                if (isset($requestData['erp_name'])) {
                    $this->erpCode = $requestData['erp_name'];
                } else {
                    $this->erpCode = __("ERP");
                }
                $this->customerGroup->setCode($this->targetCustomerGroup);
                $taxClassId = $this->customerGroup->getTaxClassId();
                if (!isset($taxClassId)) {
                    $this->customerGroup->setTaxClassId(3);
                }
                $customerGroupResponseBeforeEvent = "erpconnect_forward_customergroupresponse_before";
                $this->eventManager->dispatch($customerGroupResponseBeforeEvent, ['currentObject' => $this]);
                $result = $this->groupRepository->save($this->customerGroup);
                $customerGroupResponseAfterEvent = "erpconnect_forward_customergroupresponse_after";
                $this->eventManager->dispatch($customerGroupResponseAfterEvent, ['currentObject' => $this]);

                if ($result->getId() && is_numeric($result->getId())) {
                    $this->updateI95DevGroupDetails();
                    return $this->abstractDataPersistence->setResponse(
                        \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                        __("Response send successfully"),
                        null
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
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                self::CRITICAL
            );
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     * Validate if customer group is exists in magento or not
     *
     * @updatedBy Debashis S. Gopal
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData()
    {
        try {
            $this->customerGroup = $this->groupRepository->getById($this->customerGroupId);
            if (empty($this->customerGroup)) {
                $message = "Customer Group Not Found ::" . $this->customerGroupId;
                throw new LocalizedException(__($message));
            }
            $groupCode = $this->customerGroup->getCode();
            if ($groupCode != $this->targetCustomerGroup) {
                throw new LocalizedException(__("Input target customer group did not match with magento"));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new LocalizedException(__($ex->getMessage()));
        }
        return true;
    }

    /**
     * Update customer group details in i95dev_customer_group table.
     *
     * @createdBy Debashis S. Gopal
     */
    protected function updateI95DevGroupDetails()
    {
        try {
            $customGroupCollection = $this->i95DevGroupFactory->create()->getCollection()
                    ->addFieldToFilter('customer_group_id', $this->customerGroupId);
            if ($customGroupCollection->getSize() > 0) {
                $customGroupData = $customGroupCollection->getData();
                $customGroupModel = $this->i95DevGroupFactory->create()->load($customGroupData[0]['id']);
                $customGroupModel->setTargetGroupId($this->targetCustomerGroup);
                $currentDate = $this->date->gmtDate();
                $customGroupModel->setUpdatedAt($currentDate);
                $customGroupModel->setUpdateBy($this->erpCode);
                $customGroupModel->save();
            } else {
                $this->logger->createLog(
                    __METHOD__,
                    "Target Customer group not exists in i95dev_customer_group",
                    LoggerInterface::I95EXC,
                    self::CRITICAL
                );
            }
        } catch (LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                self::CRITICAL
            );
        }
    }
}
