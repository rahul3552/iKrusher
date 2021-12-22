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

use Aheadworks\OneStepCheckout\Api\Data\CartItemOptionsDetailsInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class CartItemOptionsDetails
 * @package Aheadworks\OneStepCheckout\Model
 */
class CartItemOptionsDetails extends AbstractSimpleObject implements CartItemOptionsDetailsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsDetails()
    {
        return $this->_get(self::OPTIONS_DETAILS);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionsDetails($optionDetails)
    {
        return $this->setData(self::OPTIONS_DETAILS, $optionDetails);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageDetails()
    {
        return $this->_get(self::IMAGE_DETAILS);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageDetails($imageDetails)
    {
        return $this->setData(self::IMAGE_DETAILS, $imageDetails);
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
}
