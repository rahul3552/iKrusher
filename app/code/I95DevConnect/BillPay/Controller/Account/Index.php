<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;

class Index
{

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * scopeConfig for system Congiguration
     *
     * @var string
     */
    protected $scopeConfig;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\App\Helper\Context $helperContext
     */
    public function __construct(
        Action\Context $context,
        Session $customerSession,
        \Magento\Framework\App\Helper\Context $helperContext
    ) {
        $this->resultFactory = $context->getResultFactory();
        $this->customerSession = $customerSession;
        $this->urlInterface = $helperContext->getUrlBuilder();
        $this->scopeConfig = $helperContext->getScopeConfig();
        $this->_view = $context->getView();
    }

    /**
     * Get links
     *
     * @return \Magento\Framework\View\Element\Html\Link[]
     */
    public function afterGetLinks(\Magento\Framework\View\Element\Html\Links $subject, $result) // NOSONAR
    {

        $customerGroupId = $this->customerSession->getCustomer()->getGroupId();
        $customerGroups = $this->scopeConfig->getValue(
            'i95devconnect_billpay/billpay_enabled_settings/enable_customergroups',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $storeGrp = explode(',', $customerGroups);
        if (!in_array($customerGroupId, $storeGrp)) {
            $layout = $this->_view->getLayout();
            $layout->unsetElement('customer-billpay-manage-payment');
            $layout->unsetElement('customer-billpay-my-payments');
            $layout->unsetElement('customer-billpay-receipt-details');
        }

        return $result;
    }
}
