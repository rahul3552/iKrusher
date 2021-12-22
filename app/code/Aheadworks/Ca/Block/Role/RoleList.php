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
namespace Aheadworks\Ca\Block\Role;

use Aheadworks\Ca\Block\Grid;

/**
 * Class RoleList
 * @package Aheadworks\Ca\Block\Role
 * @method \Aheadworks\Ca\ViewModel\Role\RoleList getRoleListViewModel()
 * @method \Aheadworks\Ca\ViewModel\Role\Role getRoleViewModel()
 */
class RoleList extends Grid
{
    /**
     * {@inheritdoc}
     */
    protected function getPagerName()
    {
        return 'aw_ca.role.list.pager';
    }

    /**
     * {@inheritdoc}
     */
    protected function getListViewModel()
    {
        return $this->getRoleListViewModel();
    }
}
