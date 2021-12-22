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
namespace Aheadworks\Ca\Model\Authorization\Acl\Loader;

use Magento\Framework\Acl\LoaderInterface;
use Magento\Framework\Acl;
use Aheadworks\Ca\Model\Source\Role\Permission\Type;

/**
 * Class Rule
 * @package Aheadworks\Ca\Model\Authorization\Acl\Loader
 */
class Rule extends Role implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function populateAcl(Acl $acl)
    {
        foreach ($this->getCompanyRoles() as $role) {
            $roleId = $role->getId();

            foreach ($role->getPermissions() as $permission) {
                $resource = $permission->getResourceId();
                if ($acl->has($resource)) {
                    if ($permission->getPermission() == Type::ALLOW) {
                        if ($resource === $this->rootResource->getId()) {
                            $acl->allow($roleId);
                        }
                        $acl->allow($roleId, $resource);
                    } elseif ($permission->getPermission() == Type::DENY) {
                        $acl->deny($roleId, $resource);
                    }
                }
            }
        }
    }
}
