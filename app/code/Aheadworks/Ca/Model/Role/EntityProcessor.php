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

use Aheadworks\Ca\Model\Role as RoleModel;

/**
 * Class EntityProcessor
 * @package Aheadworks\Ca\Model\Role
 */
class EntityProcessor
{
    /**
     * @var array[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity data before save
     *
     * @param RoleModel $object
     * @return RoleModel
     */
    public function prepareDataBeforeSave($object)
    {
        foreach ($this->processors as $processor) {
            $processor->beforeSave($object);
        }
        return $object;
    }

    /**
     * Prepare entity data after load
     *
     * @param RoleModel $object
     * @return RoleModel
     */
    public function prepareDataAfterLoad($object)
    {
        foreach ($this->processors as $processor) {
            $processor->afterLoad($object);
        }
        return $object;
    }
}
