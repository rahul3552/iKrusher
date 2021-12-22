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
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Report;

/**
 * Interface FilterableInterface
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\Report
 */
interface FilterableInterface
{
    /**
     * Add customer group filter
     *
     * @param int $groupId
     * @return $this
     */
    public function addCustomerGroupIdFilter($groupId);

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreIdFilter($storeId);

    /**
     * Add store group filter
     *
     * @param int $storeGroupId
     * @return $this
     */
    public function addStoreGroupIdFilter($storeGroupId);

    /**
     * Add website filter
     *
     * @param int $websiteId
     * @return $this
     */
    public function addWebsiteIdFilter($websiteId);

    /**
     * Add period filter
     *
     * @param string $periodFrom
     * @param string $periodTo
     * @return $this
     */
    public function addPeriodFilter($periodFrom, $periodTo);
}
