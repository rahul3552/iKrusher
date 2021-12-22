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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;

/**
 * Class PointsSummaryServicePlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class PointsSummaryServicePlugin
{
    /**
     * @var RewardPointsManagement
     */
    private $rewardPointsManagement;

    /**
     * @param RewardPointsManagement $rewardPointsManagement
     */
    public function __construct(
        RewardPointsManagement $rewardPointsManagement
    ) {
        $this->rewardPointsManagement = $rewardPointsManagement;
    }

    /**
     * Check reward points limit usage
     *
     * @param \Aheadworks\RewardPoints\Model\Service\PointsSummaryService $subject
     * @param callable $proceed
     * @param int $customerId
     * @return int
     */
    public function aroundGetCustomerRewardPointsBalance($subject, callable $proceed, $customerId)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);
        $balance = $proceed($customerId);
        $balance = $this->rewardPointsManagement->applyRewardPointsLimitIfNeeded($balance);

        return $balance;
    }
}
