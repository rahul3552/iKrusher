<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Block\Adminhtml\Edit\Tab;

use I95DevConnect\NetTerms\Model\NetTermsFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Customer;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Customer Creditlimits form block
 */
class NetTerms extends Generic implements TabInterface
{

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * scopeConfig for system Configuration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     * customer Model
     *
     * @var Customer
     */
    public $customerModel;
    public $customerData;
    protected $_template = 'I95DevConnect_NetTerms::customer/tab/netterms.phtml';

    /**
     * @var \I95DevConnect\NetTerms\Model\netTerms
     */
    public $netTerms;

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Customer $custmerModel
     * @param NetTermsFactory $netTerms
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Customer $custmerModel,
        NetTermsFactory $netTerms,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerModel = $custmerModel;
        $this->netTerms = $netTerms;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get current customer id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Get Tab label
     * @return string
     */
    public function getTabLabel()
    {
        return __('NetTerms Information');
    }

    /**
     * Get tab title
     * @return string
     */
    public function getTabTitle() // NOSONAR
    {
        return __('NetTerms Information');
    }

    /**
     * To show tab
     * @return boolean
     */
    public function canShowTab()
    {
        $this->customerData = [];
        $isEnabled = $this->scopeConfig->getValue(
            'i95devconnect_netterms/netterms_enabled_settings/enable_netterms',
            ScopeInterface::SCOPE_STORE
        );
        if ($this->getCustomerId() && $isEnabled) {
            $this->customerData  = $this->customerModel->load($this->getCustomerId())->getData();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method to hide tab in customer creation
     * @return boolean
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }

        return true;
    }

    /**
     * To get tab class
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

   /**
    * Return URL link to Tab content
    * @return string
    */
    public function getTabUrl() // NOSONAR
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     * @return boolean
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Method to edit customer data
     * @return boolean
     */
    public function canEditData() // NOSONAR
    {
        if ($this->getCustomerId()) {
            return false;
        }

        return true;
    }

    /**
     * Get target netterms id
     * @return string
     */
    public function getTargetNetTermsId()
    {
        if ($this->getCustomerId()) {
            return isset($this->customerData['net_terms_id']) ? $this->customerData['net_terms_id'] : '';
        }
    }

    /**
     * Get netterms data
     * @return array
     */
    public function getNetTermsData()
    {
        return $this->netTerms->create()
            ->getCollection()->addFieldToFilter('target_net_terms_id', ['neq' => ''])
            ->addFieldToSelect('target_net_terms_id')->getData();
    }
}
