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
namespace Aheadworks\OneStepCheckout\Model\CheckoutSection;

use Aheadworks\OneStepCheckout\Api\Data\GiftMessageSectionInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class GiftMessage
 * @package Aheadworks\OneStepCheckout\Model\CheckoutSection
 */
class GiftMessage extends AbstractSimpleObject implements GiftMessageSectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrderMessage()
    {
        return $this->_get(self::ORDER_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderMessage($message)
    {
        return $this->setData(self::ORDER_MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemMessages()
    {
        return $this->_get(self::ITEM_MESSAGES);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemMessages($messages)
    {
        return $this->setData(self::ITEM_MESSAGES, $messages);
    }
}
