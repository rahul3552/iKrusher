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
namespace Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage;

use Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class GiftMessageLevel
 * @package Aheadworks\OneStepCheckout\Model\CheckoutSection\GiftMessage
 */
class GiftMessageLevel extends AbstractSimpleObject implements GiftMessageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->_get(self::CONFIG);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($config)
    {
        return $this->setData(self::CONFIG, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
