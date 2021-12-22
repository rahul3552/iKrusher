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
namespace Aheadworks\Ctq\Api;

/**
 * Interface SellerQuoteManagementInterface
 * @api
 */
interface SellerQuoteManagementInterface
{
    /**
     * Change quote status
     *
     * @param int $quoteId
     * @param string $status
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changeStatus($quoteId, $status);

    /**
     * Create quote
     *
     * @param int $cartId
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createQuote($cartId, $quote);

    /**
     * Update quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateQuote($quote);

    /**
     * Retrieve native cart object by quote
     *
     * Native cart object will be created again in case it was removed
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface|int $quote
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCartByQuote($quote);

    /**
     * Make a quote duplication
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function copyQuote($quote);

    /**
     * Sell quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return void
     */
    public function sell($quote);
}
