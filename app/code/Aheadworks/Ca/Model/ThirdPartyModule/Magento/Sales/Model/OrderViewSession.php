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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Magento\Sales\Model;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

/**
 * Class OrderViewSession
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Magento\Sales\Model
 */
class OrderViewSession extends CustomerSession
{
    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $order = $this->getOrderRegistry()->getOrder();
        if ($order
            && $this->getAuthorizationManagement()->isAllowedByResource('Aheadworks_Ca::company_sales_sub_view')
        ) {
            $customerIds = $this->getCompanyUserManagement()->getChildUsersIds($customerId);
            $orderCustomerId = $order->getCustomerId();
            if (in_array($orderCustomerId, $customerIds)) {
                $customerId = $orderCustomerId;
            }
        }

        return $customerId;
    }

    /**
     * Retrieve authorization management
     *
     * @return AuthorizationManagementInterface
     */
    private function getAuthorizationManagement()
    {
        return ObjectManager::getInstance()->get(AuthorizationManagementInterface::class);
    }

    /**
     * Retrieve company user management
     *
     * @return CompanyUserManagementInterface
     */
    private function getCompanyUserManagement()
    {
        return ObjectManager::getInstance()->get(CompanyUserManagementInterface::class);
    }

    /**
     * Retrieve company user management
     *
     * @return OrderRegistry
     */
    private function getOrderRegistry()
    {
        return ObjectManager::getInstance()->get(OrderRegistry::class);
    }
}
