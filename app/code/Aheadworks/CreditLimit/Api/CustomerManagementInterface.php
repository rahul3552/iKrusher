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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Api;

/**
 * Interface CustomerManagementInterface
 * @api
 */
interface CustomerManagementInterface
{
    /**
     * Check if credit limit is available for customer
     *
     * @param int $customerId
     * @return bool
     */
    public function isCreditLimitAvailable($customerId);

    /**
     * Check if credit limit is configured by admin
     *
     * @param int $customerId
     * @return bool
     */
    public function isCreditLimitCustom($customerId);

    /**
     * Get credit limit amount
     *
     * @param int $customerId
     * @param string|null $currency
     * @return float|null in case it's not configured at all
     */
    public function getCreditLimitAmount($customerId, $currency = null);

    /**
     * Get credit balance amount
     *
     * @param int $customerId
     * @param string|null $currency
     * @return float
     */
    public function getCreditBalanceAmount($customerId, $currency = null);

    /**
     * Get credit available amount
     *
     * @param int $customerId
     * @param string|null $currency
     * @return float
     */
    public function getCreditAvailableAmount($customerId, $currency = null);
}
