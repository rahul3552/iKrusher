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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Plugin\Quote;

use Magento\Quote\Model\Quote\TotalsCollector as TotalsCollectorModel;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Aheadworks\OneStepCheckout\Model\CartManagement;

/**
 * Class TotalsCollector
 *
 * @package Aheadworks\OneStepCheckout\Plugin\Quote
 */
class TotalsCollector
{
    /**
     * @var CartManagement
     */
    private $cartManagement;

    /**
     * @param CartManagement $cartManagement
     */
    public function __construct(
        CartManagement $cartManagement
    ) {
        $this->cartManagement = $cartManagement;
    }

    /**
     * Reset quote discount amounts for correct recalculation
     *
     * @param \Magento\Quote\Model\Quote\TotalsCollector $subject
     * @param Quote $quote
     * @param QuoteAddress $address
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCollectAddressTotals(
        TotalsCollectorModel $subject,
        Quote $quote,
        QuoteAddress $address
    ) {
        $this->cartManagement->resetAppliedDiscounts($subject, $quote);
    }

    /**
     * Reset quote discount amounts for correct recalculation
     *
     * @param \Magento\Quote\Model\Quote\TotalsCollector $subject
     * @param Quote $quote
     *
     * @return void
     */
    public function beforeCollect(
        TotalsCollectorModel $subject,
        Quote $quote
    ) {
        $this->cartManagement->resetAppliedDiscounts($subject, $quote);
    }
}
