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
namespace Aheadworks\Ctq\Api;

/**
 * Interface BuyerPermissionManagementInterface
 * @api
 */
interface BuyerPermissionManagementInterface
{
    /**
     * Check if can buy quote or not
     *
     * @param int $quoteId
     * @return bool
     */
    public function canBuyQuote($quoteId);

    /**
     * Check if can request quote or not
     *
     * @param int $cartId
     * @return bool
     */
    public function canRequestQuote($cartId);

    /**
     * Check if can request quote list or not
     *
     * @param int $cartId
     * @return bool
     */
    public function canRequestQuoteList($cartId);

    /**
     * Check if allow quotes or not
     *
     * @param int $customerId
     * @param int $storeId
     * @return bool
     */
    public function isAllowQuotesForCustomer($customerId, $storeId);

    /**
     * Check if allow quote list
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return bool
     */
    public function isAllowQuoteList($customerGroupId, $storeId);

    /**
     * Check if allow quote update
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isAllowQuoteUpdate($websiteId = null);

    /**
     * Check if allow quote items sorting
     *
     * @param int $quoteId
     * @return bool
     * @throws \Exception
     */
    public function isAllowItemsSorting($quoteId);
}
