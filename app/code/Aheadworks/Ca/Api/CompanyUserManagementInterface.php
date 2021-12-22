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
 * Interface CompanyUserManagementInterface
 * @api
 */
interface CompanyUserManagementInterface
{
    /**
     * Retrieve current user
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCurrentUser();

    /**
     * Retrieve root user by customer id
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getRootUserForCustomer($customerId);

    /**
     * Retrieve root user by company id
     *
     * @param int $companyId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getRootUserForCompany($companyId);

    /**
     * Retrieve all user by company id
     *
     * @param int $companyId
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    public function getAllUserForCompany($companyId);

    /**
     * Retrieve all users ids by company id
     *
     * @param int $companyId
     * @return int[]
     */
    public function getAllUsersIdsForCompany($companyId);

    /**
     * Crete or update customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $user
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveUser($user);

    /**
     * Retrieve child users for user id
     *
     * @param int $userId
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    public function getChildUsers($userId);

    /**
     * Retrieve child users ids for user id
     *
     * @param int $userId
     * @return int[]
     */
    public function getChildUsersIds($userId);

    /**
     * Check if email is available for company email or customer email
     *
     * @param string $email
     * @param int $websiteId
     * @return \Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEmailAvailable($email, $websiteId = null);

    /**
     * Check if email is available for convert to company administrator
     *
     * @param string $email
     * @param int $websiteId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAvailableConvertToCompanyAdmin($email, $websiteId = null);

    /**
     * Assign user to company
     *
     * @param int $userId
     * @param int $companyId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignUserToCompany($userId, $companyId);
}
