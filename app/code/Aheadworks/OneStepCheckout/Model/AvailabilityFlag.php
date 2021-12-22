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

use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class AvailabilityFlag
 * @package Aheadworks\OneStepCheckout\Model
 */
class AvailabilityFlag
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
     * @var CheckoutHelper
     */
    private $checkoutHelper;

    /**
     * @var string|null
     */
    private $message;

    /**
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param CheckoutHelper $checkoutHelper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        CheckoutHelper $checkoutHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * Checks if checkout available
     *
     * @return bool
     */
    public function isAvailable()
    {
        $this->message = null;

        if (!$this->checkoutHelper->canOnepageCheckout()) {
            $this->message = 'One-page checkout is turned off.';
            return false;
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return false;
        }
        if (!$this->customerSession->isLoggedIn()
            && !$this->checkoutHelper->isAllowedGuestCheckout($quote)
        ) {
            $this->message = 'Guest checkout is disabled.';
            return false;
        }

        return true;
    }

    /**
     * Get message that explain why the checkout is unavailable
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }
}
