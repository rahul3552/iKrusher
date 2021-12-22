<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Controller\Index;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Customer\Model\Session;

class ManagePayment extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * manage payments
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Manage Payment'));

            $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
            if ($block) {
                $block->setRefererUrl($this->_redirect->getRefererUrl());
            }
            
            return $resultPage;
        } else {
            $this->_redirect('customer/account/login');
        }
    }
}
