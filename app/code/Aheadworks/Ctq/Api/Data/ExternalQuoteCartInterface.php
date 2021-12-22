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
namespace Aheadworks\Ctq\Api\Data;

/**
 * This interface is used for web api only
 *
 * Interface ExternalQuoteCartInterface
 * @api
 */
interface ExternalQuoteCartInterface extends QuoteCartInterface
{
    /**
     * Get quote
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getQuote();

    /**
     * Set quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return $this
     */
    public function setQuote($quote);

    /**
     * Get items
     *
     * @return \Magento\Quote\Api\Data\CartItemInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     * @return $this
     */
    public function setItems($items);

    /**
     * Get shipping address
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function getShippingAddress();

    /**
     * Set shipping address
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Get billing address
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set billing address
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @return $this
     */
    public function setBillingAddress($billingAddress);
}
