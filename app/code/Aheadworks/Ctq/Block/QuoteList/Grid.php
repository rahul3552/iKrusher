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
namespace Aheadworks\Ctq\Block\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\Provider;
use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Grid
 * @package Aheadworks\Ctq\Block\Cart
 */
class Grid extends AbstractCart
{
    /**
     * @var Provider
     */
    private $quoteProvider;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Provider $quoteProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CheckoutSession $checkoutSession,
        Provider $quoteProvider,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->quoteProvider = $quoteProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        return $this->quoteProvider->getQuote()->getAllVisibleItems();
    }

    /**
     * Get Url for update quote list
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        $params = array_merge($params, [Checker::AW_CTQ_QUOTE_LIST_FLAG => 1]);
        return parent::getUrl($route, $params);
    }
}
