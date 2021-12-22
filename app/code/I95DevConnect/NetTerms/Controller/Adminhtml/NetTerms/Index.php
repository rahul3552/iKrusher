<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Controller\Adminhtml\NetTerms;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller class
 */
class Index extends Action
{

    /**
     * @var PageFactory
     */
    public $scopeConfig;
    public $resultPageFactory;
    public $messageManager;

    /**
     * Constructor
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * Index action
     *
     * @return Page
     */
    public function execute()
    {
        /**
         * @var Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Net Terms'));
        return $resultPage;
    }
}
