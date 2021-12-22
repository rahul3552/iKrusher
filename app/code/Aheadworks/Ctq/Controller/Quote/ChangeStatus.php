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

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ChangeStatus
 * @package Aheadworks\Ctq\Controller\Quote
 */
class ChangeStatus extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if (!$this->isQuoteCanBeEdited()) {
                return $resultRedirect->setPath('*/*/');
            }
            $this->performSave(Status::PENDING_SELLER_REVIEW);
            return $this->redirectTo($resultRedirect);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while save the quote.')
            );
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * Perform save
     *
     * @param string $status
     * @return void
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function performSave($status)
    {
        $quoteId = $this->getQuote()->getId();
        $this->buyerQuoteManagement->changeStatus($quoteId, $status);
    }

    /**
     * Redirect to
     *
     * @param Redirect $resultRedirect
     * @return Redirect
     */
    protected function redirectTo($resultRedirect)
    {
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
