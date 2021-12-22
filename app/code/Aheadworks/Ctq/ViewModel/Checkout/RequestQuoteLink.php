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
namespace Aheadworks\Ctq\ViewModel\Checkout;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class RequestQuoteLink
 * @package Aheadworks\Ctq\ViewModel\Checkout
 */
class RequestQuoteLink implements ArgumentInterface
{
    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var BuyerPermissionManagementInterface
     */
    protected $buyerPermissionManagement;

    /**
     * @param CustomerUrl $customerUrl
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     */
    public function __construct(
        CustomerUrl $customerUrl,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        BuyerPermissionManagementInterface $buyerPermissionManagement
    ) {
        $this->customerUrl = $customerUrl;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->buyerPermissionManagement = $buyerPermissionManagement;
    }

    /**
     * Retrieve url for Request a Quote button
     *
     * @return string
     */
    public function getSignInUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    /**
     * Check if Request a Quote button is available
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isRequestQuoteButtonAvailable()
    {
        $cartId = $this->checkoutSession->getQuote()->getId();
        return $this->buyerPermissionManagement->canRequestQuote($cartId);
    }

    /**
     * Check is customer is authenticate
     *
     * @return bool
     */
    public function isAuthenticatedCustomer()
    {
        return $this->customerSession->isLoggedIn();
    }
}
