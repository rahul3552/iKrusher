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
 * Interface GiftMessageConfigInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface GiftMessageConfigInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const IS_ENABLED = 'is_enabled';
    const ITEM_ID = 'item_id';
    /**#@-*/

    /**
     * Retrieve is enabled flag
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Set is enabled
     *
     * @param bool $isEnabled
     * @return $this
     */
    public function setIsEnabled($isEnabled);

    /**
     * Retrieve item id
     *
     * @return int|null
     */
    public function getItemId();

    /**
     * Set item id
     *
     * @param int|null $itemId
     * @return $this
     */
    public function setItemId($itemId);
}
