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

/**
 * Class Buy
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Buy extends ChangeStatus
{
    /**
     * {@inheritdoc}
     */
    protected function performSave($status)
    {
        $quoteId = $this->getQuote()->getId();
        $storeId = $this->storeManager->getStore()->getId();
        $this->buyerQuoteManagement->buy($quoteId, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo($resultRedirect)
    {
        return $resultRedirect->setPath('checkout');
    }
}
