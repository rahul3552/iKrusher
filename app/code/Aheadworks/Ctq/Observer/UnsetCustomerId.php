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
namespace Aheadworks\Ctq\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartInterface as QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\CartInterface;

/**
 * Class UnsetCustomerId
 * @package Aheadworks\Ctq\Observer
 */
class UnsetCustomerId implements ObserverInterface
{
    /**
     * Unset customer id for quote list
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $quote = $event->getDataObject();

        if ($quote instanceof QuoteCartInterface
            && $quote->getData(CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID)
            && $quote->getIsActive()
        ) {
            $quote->unsCustomerId();
        }
    }
}
