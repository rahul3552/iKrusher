<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use I95DevConnect\MessageQueue\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http;

/**
 * Observer class to save customer custom attribute
 */
class CustomerObserver implements ObserverInterface
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

    public $dataHelper;
    public $request;

    /**
     * CustomerObserver constructor.
     *
     * @param Data $dataHelper
     * @param Registry $coreRegistry
     * @param Http $request
     */
    public function __construct(
        Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,
        Http $request
    ) {

        $this->dataHelper = $dataHelper;
        $this->coreRegistry = $coreRegistry;
        $this->request = $request;
    }

    /**
     * Save i95Dev customer Custom attributes
     * @param  \Magento\Framework\Event\Observer $observer
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
        $customer = $observer->getEvent()->getCustomer();
        $this->dataHelper->customCustomerAttributes($customer);
    }
}
