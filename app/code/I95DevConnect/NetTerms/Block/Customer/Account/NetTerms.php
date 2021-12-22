<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\NetTerms\Block\Customer\Account;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;

/**
 * Block class for customer account tab
 */
class NetTerms extends Template
{

    public $netTermsFactory;
    public $customerSession;
    public $customerFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }

    /**
     * To prepare layout
     * @return $this
     */
    public function _prepareLayout() // NOSONAR
    {
        return parent::_prepareLayout();
    }

    /**
     * To get netterm id
     * @return string
     */
    public function getNetTermId()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerData = $this->customerFactory->create()->load($customerId)->getData();
        return isset($customerData['net_terms_id']) ? $customerData['net_terms_id'] : "";
    }
}
