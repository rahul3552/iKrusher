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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CreditLimitManagement
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model
 */
class CreditLimitManagement
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        AuthorizationManagementInterface $authorizationManagement,
        RoleRepositoryInterface $roleRepository
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->authorizationManagement = $authorizationManagement;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Change customer ID if required
     *
     * @param int $customerId
     * @return int|null
     */
    public function changeCustomerIdIfNeeded($customerId)
    {
        $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);
        if ($rootCustomer) {
            $customerId = $rootCustomer->getId();
        }
        return $customerId;
    }

    /**
     * Check if available transactions
     *
     * @return bool
     */
    public function isAvailableTransactions()
    {
        return $this->authorizationManagement->isAllowedByResource('Aheadworks_CreditLimit::company_cl_transactions');
    }

    /**
     * Check if available view and use
     *
     * @return bool
     */
    public function isAvailableViewAndUse()
    {
        return $this->authorizationManagement->isAllowedByResource('Aheadworks_CreditLimit::company_cl_view_and_use');
    }

    /**
     * Retrieve root user by customer ID
     *
     * @param int $customerId
     * @return CustomerInterface|null
     */
    public function getRootUserByCustomerId($customerId)
    {
        return $this->companyUserManagement->getRootUserForCustomer($customerId);
    }
}
