<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http;

/**
 * Customer address observer
 */
class AddressCustomerObserver implements ObserverInterface
{

    /**
     * Customer save processed flag code
     */
    const CUSTOMER_SAVE_FLAG = 'customer_save_processed';
    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $data;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $attributeRepository;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /*
     * core /write  for database
     */
    public $resource;

    /*
     * connection for database connection
     */
    public $connection;

    /**
     * @var Customer Model
     */
    public $customerModel;
    public $dataHelper;
    public $request;

    /**
     * AddressCustomerObserver constructor.
     *
     * @param Registry $coreRegistry
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param Http $request
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Customer $customerModel,
        Http $request,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {

        $this->dataHelper = $dataHelper;
        $this->coreRegistry = $coreRegistry;
        $this->resource = $resource;
        $this->connection = $resource->getConnection('write');
        $this->customerModel = $customerModel;
        $this->request = $request;
    }

    /**
     * To Save i95dev Customer address Custom attributes
     * @param \Magento\Framework\Event\Observer $observer
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

        $customer = $observer->getEvent()->getDataObject()->getCustomer();
        $customerAddress = $observer->getEvent()->getDataObject();
        $updateAt = $customerAddress->getUpdatedAt();
        $customerData = $customerAddress->getCustomer();
        $customerData->setDataUsingMethod('updated_at', $updateAt);
        $this->dataHelper->customCustomerAttributes($customer);
    }
}
