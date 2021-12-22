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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Model;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CtqManagement
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Model
 */
class CtqManagement
{
    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param ObjectManagerInterface $objectManager
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        AuthorizationManagementInterface $authorizationManagement,
        ObjectManagerInterface $objectManager,
        CartRepositoryInterface $cartRepository
    ) {
        $this->authorizationManagement = $authorizationManagement;
        $this->objectManager = $objectManager;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Check if available view
     *
     * @return bool
     */
    public function isAvailableView()
    {
        return $this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_view');
    }

    /**
     * Assign customer to quote
     *
     * @param CustomerInterface $customer
     * @param int|\Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function assignCustomerToQuote($customer, $quote)
    {
        if (is_numeric($quote)) {
            $quote = $this->getQuoteRepository()->get($quote);
        }
        $cart = $this->cartRepository->get($quote->getCartId());
        $cart->assignCustomer($customer);
        $this->cartRepository->save($cart);
        $quote->setCustomerId($customer->getId());
        $quoteCart = $this->getQuoteCartConverter()->convert($cart);
        $quote->setCart($quoteCart);
        $this->getQuoteRepository()->save($quote);
    }

    /**
     * Get quote repository
     *
     * @return \Aheadworks\Ctq\Api\QuoteRepositoryInterface
     */
    public function getQuoteRepository()
    {
        return $this->objectManager->get(\Aheadworks\Ctq\Api\QuoteRepositoryInterface::class);
    }

    /**
     * Get quote cart converter
     *
     * @return \Aheadworks\Ctq\Model\Cart\ToQuoteCart
     */
    private function getQuoteCartConverter()
    {
        return $this->objectManager->get(\Aheadworks\Ctq\Model\Cart\ToQuoteCart::class);
    }
}
