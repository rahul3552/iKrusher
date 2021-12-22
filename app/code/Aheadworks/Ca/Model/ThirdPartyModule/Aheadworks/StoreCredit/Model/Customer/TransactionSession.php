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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\StoreCreditManagement;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

/**
 * Class TransactionSession
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\Customer
 */
class TransactionSession extends CustomerSession
{
    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $customerId = $this->getStoreCreditManagement()->changeCustomerIdIfNeededForTransaction($customerId);

        return $customerId;
    }

    /**
     * Retrieve store credit management
     *
     * @return StoreCreditManagement
     */
    private function getStoreCreditManagement()
    {
        return ObjectManager::getInstance()->get(StoreCreditManagement::class);
    }
}
