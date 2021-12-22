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

use Aheadworks\RewardPoints\Api\Data\SpendRateInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;

/**
 * Class RateCalculatorPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin\Customer
 */
class RateCalculatorPlugin
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
     * Calculate reward discount
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\RateCalculator $subject
     * @param int $customerId
     * @param int $points
     * @param int|null $websiteId
     * @param SpendRateInterface $rate
     * @return array
     */
    public function beforeCalculateRewardDiscount($subject, $customerId, $points, $websiteId = null, $rate = null)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdIfNeeded($customerId);

        return [$customerId, $points, $websiteId, $rate];
    }

    /**
     * Retrieve lifetime sales value for company
     *
     * @param \Aheadworks\RewardPoints\Model\Calculator\RateCalculator $subject
     * @param int $customerId
     * @param array $storeIds
     * @return array
     */
    public function beforeGetLifetimeSalesValue($subject, $customerId, $storeIds)
    {
        $customerId = $this->rewardPointsManagement->changeCustomerIdToAllCustomersIdsIfNeeded($customerId);

        return [$customerId, $storeIds];
    }
}
