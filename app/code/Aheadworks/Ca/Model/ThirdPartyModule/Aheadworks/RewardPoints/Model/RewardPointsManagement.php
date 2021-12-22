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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Model\Company\CompanyManagement;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class RewardPointsManagement
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model
 */
class RewardPointsManagement
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
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var SummaryManagement
     */
    private $summaryManagement;

    /**
     * @var CompanyManagement
     */
    private $companyManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param RoleRepositoryInterface $roleRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param SummaryManagement $summaryManagement
     * @param CompanyManagement $companyManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        AuthorizationManagementInterface $authorizationManagement,
        RoleRepositoryInterface $roleRepository,
        PriceCurrencyInterface $priceCurrency,
        SummaryManagement $summaryManagement,
        CompanyManagement $companyManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->authorizationManagement = $authorizationManagement;
        $this->roleRepository = $roleRepository;
        $this->priceCurrency = $priceCurrency;
        $this->summaryManagement = $summaryManagement;
        $this->companyManagement = $companyManagement;
    }

    /**
     * Change customer id if needed
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
     * Change customer ID to company customers IDs array if needed
     *
     * @param int $customerId
     * @return int|array
     */
    public function changeCustomerIdToAllCustomersIdsIfNeeded($customerId)
    {
        $company = $this->companyManagement->getCompanyByCustomerId($customerId);
        if (!$company) {
            return $customerId;
        }

        $customerIds = [];
        $customers = $this->companyUserManagement->getAllUserForCompany($company->getId());
        foreach ($customers as $customer) {
            $customerIds[] = $customer->getId();
        }

        return $customerIds;
    }

    /**
     * Change customer id if needed
     *
     * @param int $customerId
     * @return int|null
     */
    public function changeCustomerIdIfNeededForTransaction($customerId)
    {
        if ($this->isAvailableTransactions()) {
            $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);
            if ($rootCustomer) {
                $customerId = $rootCustomer->getId();
            }
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
        return $this->authorizationManagement->isAllowedByResource('Aheadworks_RewardPoints::company_rp_transactions');
    }

    /**
     * Check if available view and use
     *
     * @return bool
     */
    public function isAvailableViewAndUse()
    {
        return $this->authorizationManagement->isAllowedByResource('Aheadworks_RewardPoints::company_rp_view_and_use');
    }

    /**
     * Check if available subscribe options
     *
     * @return bool
     */
    public function isAvailableSubscribeOptions()
    {
        $result = true;
        if ($currentCompanyUser = $this->companyUserManagement->getCurrentUser()) {
            $result = $currentCompanyUser->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot();
        }

        return $result;
    }

    /**
     * Retrieve root user by customer id
     *
     * @param int $customerId
     * @return CustomerInterface|null
     */
    public function getRootUserByCustomerId($customerId)
    {
        return $this->companyUserManagement->getRootUserForCustomer($customerId);
    }

    /**
     * Apply limit to customer reward points balance if needed
     *
     * @param int $balance
     * @return int
     */
    public function applyRewardPointsLimitIfNeeded($balance)
    {
        try {
            $points = $balance;
            $currentUser = $this->companyUserManagement->getCurrentUser();
            if ($currentUser) {
                $points = 0;
                $companyUser = $currentUser->getExtensionAttributes()->getAwCaCompanyUser();
                if ($this->isAvailableViewAndUse()) {
                    $role = $this->roleRepository->get($companyUser->getCompanyRoleId());
                    $points = !empty($role->getAwRpBaseAmountLimit())
                        ? min($role->getAwRpBaseAmountLimit(), $balance)
                        : $balance;
                }
            }
        } catch (\Exception $exception) {
            $points = $balance;
        }

        return $points;
    }

    /**
     * Adjust reward points details if needed
     *
     * @param \Aheadworks\RewardPoints\Api\Data\CustomerRewardPointsDetailsInterface $rewardPointsDetails
     * @param int $customerId
     * @param int|null $websiteId
     * @return \Aheadworks\RewardPoints\Api\Data\CustomerRewardPointsDetailsInterface
     * @throws NoSuchEntityException
     */
    public function adjustRewardPointsDetails($rewardPointsDetails, $customerId, $websiteId = null)
    {
        $minPointsToUse = $this->summaryManagement->getMinNumberOfPointsToUse($customerId, $websiteId);
        $rewardPointsDetails->setCustomerRewardPointsOnceMinBalance($minPointsToUse);
        return $rewardPointsDetails;
    }
}
