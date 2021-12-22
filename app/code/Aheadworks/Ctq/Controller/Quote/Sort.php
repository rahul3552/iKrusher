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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class Sort
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Sort extends BuyerAction
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
            $data = (array)$this->getRequest()->getParam('sort');
            if (!$this->isQuoteCanBeEdited()) {
                return $resultRedirect->setPath('*/*/');
            }
            $this->buyerQuoteManagement->changeQuoteItemsOrder($this->getQuote()->getId(), $data);
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
