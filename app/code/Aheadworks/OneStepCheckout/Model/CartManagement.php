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
namespace Aheadworks\OneStepCheckout\Model;

use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Quote\Model\Quote;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Reward\Plugin\TotalsCollectorFactory
    as RewardTotalsCollectorPluginFactory;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\GiftCardAccount\Plugin\TotalsCollectorFactory
    as GiftCardAccountTotalsCollectorPluginFactory;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\CustomerBalance\Plugin\TotalsCollectorFactory
    as CustomerBalanceTotalsCollectorPluginFactory;

/**
 * Class CartManagement
 *
 * @package Aheadworks\OneStepCheckout\Model
 */
class CartManagement
{
    /**
     * Flag for correct resetting discounts only if necessary
     */
    const AW_OSC_QUOTE_DISCOUNTS_HAVE_BEEN_RESET_FLAG = 'aw_osc_quote_discounts_have_been_reset_flag';

    /**
     * @var RewardTotalsCollectorPluginFactory
     */
    private $rewardTotalsCollectorPluginFactory;

    /**
     * @var GiftCardAccountTotalsCollectorPluginFactory
     */
    private $giftCardAccountTotalsCollectorPluginFactory;

    /**
     * @var CustomerBalanceTotalsCollectorPluginFactory
     */
    private $customerBalanceTotalsCollectorPluginFactory;

    /**
     * @param RewardTotalsCollectorPluginFactory $rewardTotalsCollectorPluginFactory
     * @param GiftCardAccountTotalsCollectorPluginFactory $giftCardAccountTotalsCollectorPluginFactory
     * @param CustomerBalanceTotalsCollectorPluginFactory $customerBalanceTotalsCollectorPluginFactory
     */
    public function __construct(
        RewardTotalsCollectorPluginFactory $rewardTotalsCollectorPluginFactory,
        GiftCardAccountTotalsCollectorPluginFactory $giftCardAccountTotalsCollectorPluginFactory,
        CustomerBalanceTotalsCollectorPluginFactory $customerBalanceTotalsCollectorPluginFactory
    ) {
        $this->rewardTotalsCollectorPluginFactory = $rewardTotalsCollectorPluginFactory;
        $this->giftCardAccountTotalsCollectorPluginFactory = $giftCardAccountTotalsCollectorPluginFactory;
        $this->customerBalanceTotalsCollectorPluginFactory = $customerBalanceTotalsCollectorPluginFactory;
    }

    /**
     * Reset quote discount amounts for correct recalculation of totals
     *
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function resetAppliedDiscounts(
        TotalsCollector $totalsCollector,
        Quote $quote
    ) {
        if (empty($quote->getData(self::AW_OSC_QUOTE_DISCOUNTS_HAVE_BEEN_RESET_FLAG))) {
            try {
                $rewardTotalsCollectorPlugin = $this->rewardTotalsCollectorPluginFactory->create();
                $rewardTotalsCollectorPlugin->beforeCollect($totalsCollector, $quote);
            } catch (\Exception $exception) {
                $rewardTotalsCollectorPlugin = null;
            }
            try {
                $giftCardAccountTotalsCollectorPlugin = $this->giftCardAccountTotalsCollectorPluginFactory->create();
                $giftCardAccountTotalsCollectorPlugin->beforeCollect($totalsCollector, $quote);
            } catch (\Exception $exception) {
                $giftCardAccountTotalsCollectorPlugin = null;
            }
            try {
                $customerBalanceTotalsCollectorPlugin = $this->customerBalanceTotalsCollectorPluginFactory->create();
                $customerBalanceTotalsCollectorPlugin->beforeCollect($totalsCollector, $quote);
            } catch (\Exception $exception) {
                $customerBalanceTotalsCollectorPlugin = null;
            }
            $quote->setData(self::AW_OSC_QUOTE_DISCOUNTS_HAVE_BEEN_RESET_FLAG, true);
        }

        return $this;
    }
}
