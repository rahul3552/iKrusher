<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Observer;

use I95DevConnect\NetTerms\Helper\Data;
use Magento\Framework\App\RequestInterfaceFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Observer class to set coupon code from url
 */
class CustomObserver implements ObserverInterface
{

    /**
     *
     * @var RequestInterfaceFactory
     */
    public $requestFactory;
    public $dataHelper;
    public $netTermsHelper;

    /**
     * Constructor
     * @param RequestInterfaceFactory $requestFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param Data $netTermsHelper
     * @param Http $request
     */
    public function __construct(
        RequestInterfaceFactory $requestFactory,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        Data $netTermsHelper,
        Http $request
    ) {
        $this->requestFactory = $requestFactory;
        $this->dataHelper = $dataHelper;
        $this->netTermsHelper = $netTermsHelper;
        $this->request = $request;
    }

    /**
     * Save i95Dev Custom attributes
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $isEnabled = $this->netTermsHelper->isNetTermsEnabled();
        $is_enabled = $this->dataHelper->isEnabled();
        if (!$is_enabled) {
            return;
        }

        if (!$isEnabled || $this->dataHelper->getGlobalValue('i95_observer_skip')
            || $this->request->getParam('isI95DevRestReq') == 'true') {
            return;
        }

        $request = $this->requestFactory->create();
        $data = (array) $request->getPost()['customer'];
        $customer = $observer->getCustomer();
        $netTermsId = isset($data['target_net_terms_id']) ? $data['target_net_terms_id'] : null;
        if ($netTermsId != null) {
            $customer->setData('net_terms_id', $netTermsId)->getResource()
                ->saveAttribute($customer, 'net_terms_id');
        }
    }
}
