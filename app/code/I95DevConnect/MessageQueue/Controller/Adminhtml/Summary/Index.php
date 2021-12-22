<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Controller\Adminhtml\Summary;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller for Inbound Summary
 */
class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $helperData;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $urlInterface;

    /**
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $helperData
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \I95DevConnect\MessageQueue\Helper\Data $helperData,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
        $this->urlInterface = $urlInterface;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('I95DevConnect_MessageQueue::report');
    }

    /**
     * Render inbound summary
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($this->helperData->isEnabled()) {
            $breadcrumb = ['label' => 'Summary Report', 'title' => 'Inbound Summary Report'];
            return $this->helperData->loadPage(
                'Inbound Summary Report',
                'I95DevConnect\MessageQueue\Block\Adminhtml\Summary',
                'I95DevConnect_MessageQueue::SummaryReport',
                $breadcrumb
            );
        } else {
            return $this->helperData->returnToIndex();
        }
    }
}
