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
namespace Aheadworks\Ctq\Model\QuoteList\Cart;

use Aheadworks\Ctq\Model\QuoteList\Provider;
use Aheadworks\Ctq\Model\QuoteList\State;
use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class ConfigProvider
 * @package Aheadworks\Ctq\Model\QuoteList\Cart
 */
class ConfigProvider extends CompositeConfigProvider
{
    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * @var Quote
     */
    private $_quote;

    /**
     * @var Provider
     */
    private $quoteProvider;

    /**
     * @var State
     */
    private $state;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param CheckoutSession $session
     * @param Provider $quoteProvider
     * @param State $state
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param array $configProviders
     */
    public function __construct(
        CheckoutSession $session,
        Provider $quoteProvider,
        State $state,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        array $configProviders
    ) {
        parent::__construct($configProviders);
        $this->session = $session;
        $this->quoteProvider = $quoteProvider;
        $this->state = $state;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        $this->_quote = $this->session->getQuote();
        if ($this->quoteProvider->getQuoteId()) {
            $config = $this->state->emulateQuote([$this, 'parent::getConfig']);

            return $this->prepareConfig($config);
        } else {
            return parent::getConfig();
        }
    }

    /**
     * Set quote list flag to checkout config
     *
     * @param array $config
     * @return array
     * @throws LocalizedException
     */
    private function prepareConfig($config)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $config['isCustomerLoggedIn'] = false;
        $config['isQuoteList'] = true;
        $config['quoteData']['entity_id'] = $quoteIdMask->load(
            $this->quoteProvider->getQuoteId(),
            'quote_id'
        )->getMaskedId();

        return $config;
    }
}
