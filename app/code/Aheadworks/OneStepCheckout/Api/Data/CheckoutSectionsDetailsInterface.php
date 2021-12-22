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
namespace Aheadworks\OneStepCheckout\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CheckoutSectionsDetailsInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface CheckoutSectionsDetailsInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const PAYMENT_METHODS = 'payment_methods';
    const SHIPPING_METHODS = 'shipping_methods';
    const TOTALS = 'totals';
    const GIFT_MESSAGE = 'gift_message';
    /**#@-*/

    /**
     * Get payment methods
     *
     * @return \Magento\Quote\Api\Data\PaymentMethodInterface[]|null
     */
    public function getPaymentMethods();

    /**
     * Set payment methods
     *
     * @param \Magento\Quote\Api\Data\PaymentMethodInterface[] $paymentMethods
     * @return $this
     */
    public function setPaymentMethods($paymentMethods);

    /**
     * Get shipping methods
     *
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]|null
     */
    public function getShippingMethods();

    /**
     * Set shipping methods
     *
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface[] $shippingMethods
     * @return $this
     */
    public function setShippingMethods($shippingMethods);

    /**
     * Get totals
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface|null
     */
    public function getTotals();

    /**
     * Set totals
     *
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return $this
     */
    public function setTotals($totals);

    /**
     * Get gift message
     *
     * @return \Aheadworks\OneStepCheckout\Api\Data\GiftMessageSectionInterface|null
     */
    public function getGiftMessage();

    /**
     * Set gift message
     *
     * @param \Aheadworks\OneStepCheckout\Api\Data\GiftMessageSectionInterface $giftMessage
     * @return $this
     */
    public function setGiftMessage($giftMessage);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionsDetailsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionsDetailsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionsDetailsExtensionInterface $extensionAttributes
    );
}
