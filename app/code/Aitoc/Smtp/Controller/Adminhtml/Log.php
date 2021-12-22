<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_Smtp
 */


namespace Aitoc\Smtp\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Aitoc\Smtp\Controller\RegistryConstants;

abstract class Log extends Action
{
    const DEFAULT_ERROR_MESSAGE  = 'Something went wrong. See the error log.';

    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $resultPageFactory;

    /** @var \Magento\Framework\Registry */
    protected $registry;

    /** @var \Aitoc\ShippingRules\Model\RuleFactory */
    protected $ruleFactory;

    const ADMIN_RESOURCE = 'Aitoc_Smtp::log';

    /**
     * Rule constructor.
     *
     * @param Action\Context                             $context
     * @param \Aitoc\ShippingRules\Model\RuleFactory            $ruleFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aitoc\Smtp\Model\LogFactory $ruleFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aitoc_Smtp::log');
        $resultPage->addBreadcrumb(__('Reports'), __('Reports'));
        return $resultPage;
    }

    /**
     * @param bool $requireId
     * @return \Aitoc\Smtp\Model\Log|\Magento\Framework\App\ResponseInterface
     */
    protected function getRule($requireId = false)
    {
        $ruleId = $this->getRequest()->getParam(RegistryConstants::RULE_PARAM_URL_KEY);
        if ($requireId && !$ruleId) {
            $this->messageManager->addErrorMessage(__('Log doesn\'t exist.'));
            return $this->redirectIndex();
        }

        $model = $this->ruleFactory->create();

        if ($ruleId) {
            $model->load($ruleId);
        }

        if ($ruleId && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('Log doesn\'t exist.'));
            return $this->redirectIndex();
        }

        $this->registry->register(RegistryConstants::CURRENT_RULE, $model);

        return $model;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirectIndex()
    {
        return $this->_redirect('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aitoc_Smtp::log');
    }
}
