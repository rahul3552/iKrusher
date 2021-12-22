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
namespace Aheadworks\Ca\Model\Authorization\Acl;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Authorization\RoleLocatorInterface;

/**
 * Class RoleLocator
 * @package Aheadworks\Ca\Model\Authorization\Authorization\Acl
 */
class RoleLocator implements RoleLocatorInterface
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclRoleId()
    {
        $roleId = 0;
        if ($user = $this->companyUserManagement->getCurrentUser()) {
            $roleId = $user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyRoleId();
        }
        
        return $roleId;
    }
}
