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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Edit
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Edit extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $storeManager,
            $buyerQuoteManagement,
            $buyerPermissionManagement,
            $quoteRepository
        );
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $cart = $this->getCart();
        if ($cart && $cart->getAwCtqQuoteIsChanged()) {
            $this->messageManager->addNoticeMessage(
                __('This Quote has been updated for some reasons. All details are in the History Log.')
            );
        }
        $quote = $this->getQuote();
        $resultPage->getConfig()->getTitle()->set(__('Quote %1', $quote->getName()));
        $this->setBackLink($resultPage);

        return $resultPage;
    }
}
