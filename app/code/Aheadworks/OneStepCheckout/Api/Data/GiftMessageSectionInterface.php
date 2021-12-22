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

/**
 * Interface GiftMessageSectionInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface GiftMessageSectionInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const ORDER_MESSAGE = 'order_message';
    const ITEM_MESSAGES = 'item_messages';
    /**#@-*/

    /**
     * Retrieve order level gift message
     *
     * @return \Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterface|null
     */
    public function getOrderMessage();

    /**
     * Set order level gift message
     *
     * @param \Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterface|null $message
     * @return $this
     */
    public function setOrderMessage($message);

    /**
     * Retrieve item level gift messages
     *
     * @return \Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterface[]|null
     */
    public function getItemMessages();

    /**
     * Set item level gift messages
     *
     * @param \Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterface[]|null $messages
     * @return $this
     */
    public function setItemMessages($messages);
}
