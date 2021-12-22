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
namespace Aheadworks\Ctq\Model\Quote\Status;

/**
 * Interface RestrictionsInterface
 * @package Aheadworks\Ctq\Model\Quote\Status
 */
interface RestrictionsInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const NEXT_AVAILABLE_STATUSES = 'next_available_statuses';
    const SELLER_AVAILABLE_ACTIONS = 'seller_available_actions';
    const BUYER_AVAILABLE_ACTIONS = 'buyer_available_actions';
    /**#@-*/

    /**
     * Retrieve next available statuses for current status
     *
     * @return array
     */
    public function getNextAvailableStatuses();

    /**
     * Retrieve available actions for seller
     *
     * @return array
     */
    public function getSellerAvailableActions();

    /**
     * Retrieve available actions for buyer
     *
     * @return array
     */
    public function getBuyerAvailableActions();
}
