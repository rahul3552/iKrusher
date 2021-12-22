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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Api;

/**
 * Interface CustomerManagementInterface
 * @api
 */
interface CustomerManagementInterface
{
    /**
     * Check if quick order is active for customer group
     *
     * @param int $customerGroupId
     * @param int|null $websiteId
     * @return bool
     */
    public function isActiveForCustomerGroup($customerGroupId, $websiteId = null);
}
