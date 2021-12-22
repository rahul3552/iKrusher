<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Observer\Forward;

use I95DevConnect\MessageQueue\Helper\Data;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \I95DevConnect\MessageQueue\Model\Logger;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\ScopeInterface;

/**
 * Customer response observer to update netterm id of customer
 */
class CustomerResponseObserver implements ObserverInterface
{

    public $logger;

    /**
     *
     * @var Data
     */
    public $data;

    /**
     *
     * @var Customer
     */
    public $customerModel;

    /**
     * Constructor
     * @param Logger $logger
     * @param CustomerFactory $customerModel
     * @param Http $request
     * @param Data $dataHelper
     */
    public function __construct(
        Logger $logger,
        CustomerFactory $customerModel,
        Http $request,
        Data $dataHelper
    ) {
        $this->logger             = $logger;
        $this->customerModel      = $customerModel;
        $this->request = $request;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Set i95Dev Custom attributes
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $currentObject = $observer->getEvent()->getData("currentObject");

        $inputData = $currentObject->stringData['inputData'];
        $component = $this->dataHelper->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            ScopeInterface::SCOPE_WEBSITE
        );

        if ($component == "GP" && isset($inputData['targetNetTermsId'])) {
            $customer = $this->customerModel->create()->load($currentObject->customerId);
            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute('net_terms_id', $inputData['targetNetTermsId']);
            $customer->updateData($customerData);
            $customer->save();
        }
    }
}
