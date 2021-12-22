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
namespace Aheadworks\Ctq\Model\Quote\Permission\Checker;

use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;

/**
 * Class CustomerGroup
 * @package Aheadworks\Ctq\Model\Quote\Permission\Checker
 */
class CustomerGroup
{
    /**
     * @var BuyerPermissionManagementInterface
     */
    private $buyerPermissionManagement;

    /**
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     */
    public function __construct(
        BuyerPermissionManagementInterface $buyerPermissionManagement
    ) {
        $this->buyerPermissionManagement = $buyerPermissionManagement;
    }

    /**
     * Check allow customer to quote by customer group
     *
     * @param int $customerId
     * @param int $storeId
     * @return bool
     */
    public function check($customerId, $storeId)
    {
        return $this->buyerPermissionManagement->isAllowQuotesForCustomer($customerId, $storeId);
    }
}
