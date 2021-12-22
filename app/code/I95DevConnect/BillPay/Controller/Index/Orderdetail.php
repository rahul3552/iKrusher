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

class Orderdetail extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Receipt details class
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->createBlock(
                'I95DevConnect\BillPay\Block\Index\OrderDetail'
            )->setTemplate('index/orderdetails.phtml')->toHtml();
            return $resultPage;
        } else {
            $this->_redirect('customer/account/login');
        }
    }
}
