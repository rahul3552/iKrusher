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
namespace Aheadworks\Ca\Model\ResourceModel\Group;

use Aheadworks\Ca\Model\Group;
use Aheadworks\Ca\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\ResourceModel\Group as GroupResource;

/**
 * Class Collection
 * @package Aheadworks\Ca\Model\ResourceModel\Group
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Group::class, GroupResource::class);
    }
}
