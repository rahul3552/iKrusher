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

use Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionsDetailsInterface;
use Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionsDetailsExtensionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class CheckoutSectionsDetails
 * @package Aheadworks\OneStepCheckout\Model
 */
class CheckoutSectionsDetails extends AbstractExtensibleObject implements CheckoutSectionsDetailsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods()
    {
        return $this->_get(self::PAYMENT_METHODS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethods($paymentMethods)
    {
        return $this->setData(self::PAYMENT_METHODS, $paymentMethods);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethods()
    {
        return $this->_get(self::SHIPPING_METHODS);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethods($shippingMethods)
    {
        return $this->setData(self::SHIPPING_METHODS, $shippingMethods);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals()
    {
        return $this->_get(self::TOTALS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotals($totals)
    {
        return $this->setData(self::TOTALS, $totals);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftMessage()
    {
        return $this->_get(self::GIFT_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftMessage($giftMessage)
    {
        return $this->setData(self::GIFT_MESSAGE, $giftMessage);
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
    public function setExtensionAttributes(
        CheckoutSectionsDetailsExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
