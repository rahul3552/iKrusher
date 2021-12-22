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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Controller\Index;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Aheadworks\QuickOrder\Api\CustomerManagementInterface;

/**
 * Class Index
 *
 * @package Aheadworks\CreditLimit\Controller\Balance
 */
class Index extends Action
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        CustomerManagementInterface $customerManagement
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerManagement = $customerManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Quick Order'));

        return $resultPage;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $isActive = $this->customerManagement->isActiveForCustomerGroup(
            $this->customerSession->getCustomerGroupId(),
            $this->storeManager->getWebsite()->getId()
        );
        if (!$isActive) {
            $this->getResponse()->setRedirect($this->_url->getBaseUrl());
        }

        return parent::dispatch($request);
    }
}
