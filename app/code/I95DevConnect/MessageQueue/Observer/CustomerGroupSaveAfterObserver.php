<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Observer class for customer group save
 */
class CustomerGroupSaveAfterObserver implements ObserverInterface
{

    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';

    /**
     * Product save processed flag code
     */
    const CUSTOMER_GROUP_SAVE_FLAG = 'customer_group_save_processed';

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var date
     */
    public $date;

    /**
     * @var customerGroup
     */
    public $customerGroup;

    public $logger;

    /**
     * @var \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup
     */
    public $customerGroupPersistence;

    /**
     * @var Http
     */
    public $request;

    /**
     * CustomerGroupSaveAfterObserver constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \I95DevConnect\MessageQueue\Model\CustomerGroup $customerGroup
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup $customerGroupPersistence
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param Http $request
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \I95DevConnect\MessageQueue\Model\CustomerGroup $customerGroup,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup $customerGroupPersistence,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        Http $request
    ) {

        $this->dataHelper = $dataHelper;
        $this->coreRegistry = $coreRegistry;
        $this->date = $date;
        $this->customerGroup = $customerGroup;
        $this->customerGroupPersistence = $customerGroupPersistence;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * create and update customer group
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $is_enabled = $this->dataHelper->isEnabled();
        if (!$is_enabled) {
            return;
        }
        if ($this->dataHelper->getGlobalValue('i95_observer_skip') ||
            $this->request->getParam('isI95DevRestReq') == 'true') {
            return;
        }
        try {
            $customerGroupObj = $observer->getEvent()->getDataObject();
            $customerGroupData = $customerGroupObj->getData();
            $currentDate = $this->date->gmtDate();
            $customerGroupId = isset($customerGroupData['customer_group_id']) ?
                    $customerGroupData['customer_group_id'] : '';
            $customGroup = $this->customerGroupPersistence->getCustomerGroupById($customerGroupId);

            if (!isset($customGroup['id'])) {
                $customGroupModel = $this->customerGroup;
                $customGroupModel->setcreatedAt($currentDate);
            } else {
                $customerGroupId = $customGroup['id'];
                $customGroupModel = $this->customerGroup->load($customGroup['id']);
            }
            $customGroupModel->setcustomerGroupId($customerGroupId);
            $customGroupModel->setupdatedAt($currentDate);
            $customGroupModel->setupdateBy('Magento');
            $customGroupModel->save();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
    }
}
