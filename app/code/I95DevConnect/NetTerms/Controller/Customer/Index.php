<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use \Magento\Framework\View\Result\PageFactory;

/**
 * Controller class for Index
 */
class Index extends Action
{

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     *
     * @var Session
     */
    public $customerSession;

    /**
     * Constructor
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
     * Index action for logged in customer
     * @return Page
     */
    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            /** @var Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Net Terms'));

            $block = $resultPage->getLayout()->getBlock('customer.account.netterms');
            if ($block) {
                $block->setRefererUrl($this->_redirect->getRefererUrl());
            }

            return $resultPage;
        } else {
            $this->_redirect('customer/account/login');
        }
    }
}
