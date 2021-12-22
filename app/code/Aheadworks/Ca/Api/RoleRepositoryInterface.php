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
 * Interface RoleRepositoryInterface
 * @api
 */
interface RoleRepositoryInterface
{
    /**
     * Save role
     *
     * @param \Aheadworks\Ca\Api\Data\RoleInterface $role
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Ca\Api\Data\RoleInterface $role);

    /**
     * Retrieve role by id
     *
     * @param int $roleId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($roleId);

    /**
     * Retrieve role by id
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDefaultUserRole($companyId);

    /**
     * Retrieve role list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\RoleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
