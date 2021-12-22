<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Controller\Balance;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\App\RequestInterface;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Index
 *
 * @package Aheadworks\CreditLimit\Controller\Balance
 */
class Index extends Action
{
    /**
     * Customer session model
     *
     * @var Session
     */
    private $customerSession;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerManagementInterface $customerManagement
    ) {
        $this->customerSession = $customerSession;
        $this->customerManagement = $customerManagement;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Credit Details'));

        return $resultPage;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        $customerId = $this->customerSession->getCustomerId();
        $canUseCreditLimit = $this->customerManagement->isCreditLimitAvailable($customerId);
        if ($this->customerSession->authenticate() && !$canUseCreditLimit) {
            $this->getResponse()->setRedirect($this->_url->getBaseUrl());
        }

        return parent::dispatch($request);
    }
}
