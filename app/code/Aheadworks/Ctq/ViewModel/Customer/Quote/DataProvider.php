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
namespace Aheadworks\Ctq\ViewModel\Customer\Quote;

use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Directory\Model\PriceCurrency;

/**
 * Class DataProvider
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote
 */
class DataProvider implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var PriceCurrency
     */
    private $priceCurrency;

    /**
     * @var string
     */
    private $quoteIdParamName;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $quoteRepository
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param QuoteInterfaceFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $timezone
     * @param PriceCurrency $priceCurrency
     * @param string $quoteIdParamName
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $quoteRepository,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        QuoteInterfaceFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        TimezoneInterface  $timezone,
        PriceCurrency $priceCurrency,
        $quoteIdParamName = 'quote_id'
    ) {
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->quoteIdParamName = $quoteIdParamName;
        $this->timezone = $timezone;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Retrieve active quote
     *
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote()
    {
        $quoteId = $this->request->getParam($this->quoteIdParamName);
        if (!$quoteId) {
            $quoteId = $this->request->getParam('quote_id');
        }
        if ($quoteId) {
            $storeId = $this->storeManager->getStore()->getId();
            $this->buyerQuoteManagement->getCartByQuote($quoteId, $storeId);
            $quote = $this->quoteRepository->get($quoteId);
        } else {
            $quote = $this->quoteFactory->create();
        }

        return $quote;
    }

    /**
     * Retrieve active quote id
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteId()
    {
        $quoteId = $this->request->getParam($this->quoteIdParamName);
        if (!$quoteId) {
            $quoteId = $this->request->getParam('quote_id');
        }

        return $quoteId ? $this->quoteRepository->get($quoteId)->getId() : $quoteId;
    }

    /**
     * Retrieve cart
     *
     * @return CartInterface|Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCart()
    {
        $quote = $this->getQuote();
        $storeId = $this->storeManager->getStore()->getId();
        /** @var CartInterface|Quote $cart */
        $cart = $this->buyerQuoteManagement->getCartByQuote($quote, $storeId);

        return $cart;
    }

    /**
     * Get date of update
     *
     * @param Quote $quote
     * @return string
     * @throws \Exception
     */
    public function getUpdatedAt($quote)
    {
        $updatedAt = $quote->getUpdatedAt();
        if (!$updatedAt) {
            $updatedAt = $quote->getCreatedAt();
        }

        return $this->timezone->date(new \DateTime($updatedAt))->format('d-m-Y');
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    /**
     * Get total
     *
     * @param Quote $cart
     * @return float|string
     */
    public function getTotal($cart)
    {
        $totals = $cart->getTotals();
        $subtotal = $totals['subtotal'];

        return $this->priceCurrency->format($subtotal->getValue());
    }
}
