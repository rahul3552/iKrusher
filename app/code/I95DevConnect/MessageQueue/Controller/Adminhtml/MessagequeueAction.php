<?php

namespace I95DevConnect\MessageQueue\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class MessagequeueAction
 * @package I95DevConnect\MessageQueue\Controller\Adminhtml
 */
abstract class MessagequeueAction extends \Magento\Backend\App\Action
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
}
