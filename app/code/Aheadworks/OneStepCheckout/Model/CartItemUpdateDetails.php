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

use Aheadworks\OneStepCheckout\Api\Data\CartItemUpdateDetailsInterface;
use Aheadworks\OneStepCheckout\Api\Data\CartItemUpdateDetailsExtensionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class CartItemUpdateDetails
 * @package Aheadworks\OneStepCheckout\Model
 */
class CartItemUpdateDetails extends AbstractExtensibleObject implements CartItemUpdateDetailsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCartDetails()
    {
        return $this->_get(self::CART_DETAILS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartDetails($cartDetails)
    {
        return $this->setData(self::CART_DETAILS, $cartDetails);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentDetails()
    {
        return $this->_get(self::PAYMENT_DETAILS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentDetails($paymentDetails)
    {
        return $this->setData(self::PAYMENT_DETAILS, $paymentDetails);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(CartItemUpdateDetailsExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
