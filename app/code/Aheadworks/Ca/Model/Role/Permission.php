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
namespace Aheadworks\Ca\Model\Role;

use Aheadworks\Ca\Api\Data\RolePermissionInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Permission
 * @package Aheadworks\Ca\Model\Role
 */
class Permission extends AbstractModel implements RolePermissionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getResourceId()
    {
        return $this->getData(self::RESOURCE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setResourceId($resourceId)
    {
        return $this->setData(self::RESOURCE_ID, $resourceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission()
    {
        return $this->getData(self::PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($permission)
    {
        return $this->setData(self::PERMISSION, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\RolePermissionExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
