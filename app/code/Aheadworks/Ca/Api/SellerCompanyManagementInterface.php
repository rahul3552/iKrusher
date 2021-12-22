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
namespace Aheadworks\Ca\Api;

/**
 * Interface SellerCompanyManagementInterface
 * @api
 */
interface SellerCompanyManagementInterface
{
    /**
     * Create company
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyInterface $company
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     */
    public function createCompany($company, $customer);

    /**
     * Update company
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyInterface $company
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     */
    public function updateCompany($company, $customer);

    /**
     * Check if company blocked
     *
     * @param int $companyId
     * @return bool
     */
    public function isBlockedCompany($companyId);

    /**
     * Change company status
     *
     * @param int $companyId
     * @param string $status
     * @return bool
     */
    public function changeStatus($companyId, $status);

    /**
     * Retrieve company by customer id
     * @param int $customerId
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface|null
     */
    public function getCompanyByCustomerId($customerId);
}
