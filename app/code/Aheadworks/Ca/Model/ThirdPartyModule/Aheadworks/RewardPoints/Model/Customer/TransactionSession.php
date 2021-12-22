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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\RewardPointsManagement;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

/**
 * Class TransactionSession
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\Customer
 */
class TransactionSession extends CustomerSession
{
    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $customerId = $this->getRewardPointsManagement()->changeCustomerIdIfNeededForTransaction($customerId);

        return $customerId;
    }

    /**
     * Retrieve reward points management
     *
     * @return RewardPointsManagement
     */
    private function getRewardPointsManagement()
    {
        return ObjectManager::getInstance()->get(RewardPointsManagement::class);
    }
}
