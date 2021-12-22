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
use Aheadworks\Ctq\Model\Source\Quote\Export\Type;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\Quote\Export\Composite as QuoteExporter;

/**
 * Class Export
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Export extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * @var QuoteExporter
     */
    private $quoteExporter;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteExporter $quoteExporter
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository,
        QuoteExporter $quoteExporter
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $storeManager,
            $buyerQuoteManagement,
            $buyerPermissionManagement,
            $quoteRepository
        );
        $this->quoteExporter = $quoteExporter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            return $this->quoteExporter->exportQuote($this->getQuote(), Type::DOC);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
