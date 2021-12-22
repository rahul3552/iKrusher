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
 * Interface GiftMessageInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface GiftMessageInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const CONFIG = 'config';
    const MESSAGE = 'message';
    /**#@-*/

    /**
     * Retrieve config
     *
     * @return \Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterface
     */
    public function getConfig();

    /**
     * Set config
     *
     * @param \Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterface $config
     * @return $this
     */
    public function setConfig($config);

    /**
     * Retrieve gift message
     *
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getMessage();

    /**
     * Set gift message
     *
     * @param \Magento\GiftMessage\Api\Data\MessageInterface|null $message
     * @return $this
     */
    public function setMessage($message);
}
