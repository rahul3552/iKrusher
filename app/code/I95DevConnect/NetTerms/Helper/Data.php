<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Helper;

use I95DevConnect\NetTerms\Model\NetTermsFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Helper class
 */
class Data extends AbstractHelper
{
    public $customerFactory;
    public $netTermFactory;
    public $_scopeConfig;

    const TARGETNETTERMSID = 'target_net_terms_id';

    /**
     * Constructor
     * @param Context $context
     * @param CustomerFactory $customerFactory
     * @param NetTermsFactory $nettermFactory
     */
    public function __construct(
        Context $context,
        CustomerFactory $customerFactory,
        NetTermsFactory $nettermFactory
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->customerFactory = $customerFactory;
        $this->netTermFactory=$nettermFactory;
        parent::__construct($context);
    }

    /**
     * To check netterms module is enabled or not
     * @return boolean
     */
    public function isNetTermsEnabled()
    {
        return $this->_scopeConfig
            ->getValue(
                'i95devconnect_netterms/netterms_enabled_settings/enable_netterms',
                ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * To check netterms label for payment method is enabled or not
     * @return boolean
     */
    public function isNetTermsLabelEnabled()
    {
        return $this->_scopeConfig
            ->getValue(
                'i95devconnect_netterms/netterms_enabled_settings/enable_netterms_payment_label',
                ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * To get customer netterm
     * @param string $customerId
     * @return string
     */
    public function getCustomerNetTerm($customerId)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $customerData = $customer->getData();
        $netTermsId = isset($customerData['net_terms_id']) ? $customerData['net_terms_id'] : "";
        return $this->getNetTermData($netTermsId);
    }

    /**
     * Get target netterm id and description
     * @param string $netTermsId
     * @return string
     */
    public function getNetTermData($netTermsId)
    {
        $nettermCollection=$this->netTermFactory->create()
            ->getCollection()->addFieldtoFilter(self::TARGETNETTERMSID, $netTermsId)->getData();
        $component = $this->_scopeConfig
            ->getValue(
                'i95dev_messagequeue/I95DevConnect_settings/component',
                ScopeInterface::SCOPE_STORE
            );
        // @codingStandardsIgnoreStart
        if ($component == 'NAV') {
            return isset($nettermCollection[0][self::TARGETNETTERMSID]) ? $nettermCollection[0][self::TARGETNETTERMSID] : "";
        } else {
            return isset($nettermCollection[0]['description']) ? $nettermCollection[0]['description'] : "";
        }
        // @codingStandardsIgnoreEnd
    }
}
