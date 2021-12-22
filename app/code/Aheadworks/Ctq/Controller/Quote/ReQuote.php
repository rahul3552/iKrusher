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

use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReQuote
 *
 * @package Aheadworks\Ctq\Controller\Quote
 */
class ReQuote extends ChangeStatus
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $quote = $this->buyerQuoteManagement->copyQuote($this->getQuote());
            return $resultRedirect->setPath('*/*/edit', ['quote_id' => $quote->getId(), '_current' => true]);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while duplicating the quote.')
            );
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
