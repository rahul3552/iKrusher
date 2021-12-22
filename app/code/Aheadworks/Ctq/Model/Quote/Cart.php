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
namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Cart
 * @package Aheadworks\Ctq\Model\Quote
 */
class Cart extends AbstractModel implements QuoteCartInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQuote()
    {
        return $this->getData(self::QUOTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuote($quote)
    {
        return $this->setData(self::QUOTE, $quote);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress($shippingAddress)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->getData(self::BILLING_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress($billingAddress)
    {
        return $this->setData(self::BILLING_ADDRESS, $billingAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Ctq\Api\Data\QuoteCartExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
