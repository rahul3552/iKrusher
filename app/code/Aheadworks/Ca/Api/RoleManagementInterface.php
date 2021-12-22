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
 * Interface RoleManagementInterface
 * @api
 */
interface RoleManagementInterface
{
    /**
     * Create default role
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createDefaultRole($companyId);

    /**
     * Create default user role
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createDefaultUserRole($companyId);

    /**
     * Save role
     *
     * @param \Aheadworks\Ca\Api\Data\RoleInterface $role
     * @param string[] $postedResources
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveRole($role, $postedResources);
}
