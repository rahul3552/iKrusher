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

use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Controller\BuyerAction;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\Attachment\File\Downloader as FileDownloader;

/**
 * Class Download
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Download extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param CommentManagementInterface $commentManagement
     * @param FileDownloader $fileDownloader
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        BuyerPermissionManagementInterface $buyerPermissionManagement,
        QuoteRepositoryInterface $quoteRepository,
        CommentManagementInterface $commentManagement,
        FileDownloader $fileDownloader
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $storeManager,
            $buyerQuoteManagement,
            $buyerPermissionManagement,
            $quoteRepository
        );
        $this->commentManagement = $commentManagement;
        $this->fileDownloader = $fileDownloader;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $quoteId = $this->getQuote()->getId();
        try {
            $attachment = $this->commentManagement->getAttachment(
                $this->getRequest()->getParam('file'),
                $this->getRequest()->getParam('comment_id'),
                $quoteId
            );
            return $this->fileDownloader->download($attachment);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
