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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Controller\Company;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Create
 *
 * @package Aheadworks\Ca\Controller\Company
 */
class Create extends Action
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * Check if customer is logged in
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        if ($this->customerSession->isLoggedIn()) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('New Company'));

        return $resultPage;
    }
}
