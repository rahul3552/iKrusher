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
namespace Aheadworks\Ctq\Model\QuoteList;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Api\QuoteListManagementInterface;

/**
 * Class MergeProcessor
 * For further not logged in users support
 *
 * @package Aheadworks\Ctq\Model\QuoteList
 */
class MergeProcessor
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var QuoteListManagementInterface
     */
    private $quoteListManagement;

    /**
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param QuoteListManagementInterface $quoteListManagement
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        QuoteListManagementInterface $quoteListManagement
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->quoteListManagement = $quoteListManagement;
    }

    /**
     * Merge quotes
     *
     * @return void
     */
    public function mergeQuotes()
    {
        try {
            $currentQuote = $this->quoteListManagement->getQuoteList($this->checkoutSession->getAwCtqQuoteListId());
            $customerQuote = $this->quoteListManagement->getQuoteListForCustomer(
                $this->customerSession->getCustomerId()
            );
            if ($customerQuote && $currentQuote->getId() != $customerQuote->getId()) {
                $this->quoteListManagement->mergeQuoteLists($customerQuote, $currentQuote);
            }
            $this->checkoutSession->setAwCtqQuoteListId($customerQuote->getId());
        } catch (LocalizedException $e) {
        }
    }
}
