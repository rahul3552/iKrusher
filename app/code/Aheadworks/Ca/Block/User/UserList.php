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
namespace Aheadworks\Ca\Block\User;

use Aheadworks\Ca\Block\Grid;

/**
 * Class UserList
 * @package Aheadworks\Ca\Block\User
 * @method \Aheadworks\Ca\ViewModel\User\UserList getUserListViewModel()
 * @method \Aheadworks\Ca\ViewModel\User\User getUserViewModel()
 */
class UserList extends Grid
{
    /**
     * {@inheritdoc}
     */
    protected function getPagerName()
    {
        return 'aw_ca.customer.user.list.pager';
    }

    /**
     * {@inheritdoc}
     */
    protected function getListViewModel()
    {
        return $this->getUserListViewModel();
    }
}
