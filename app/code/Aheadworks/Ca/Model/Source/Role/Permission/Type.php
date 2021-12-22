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
namespace Aheadworks\Ca\Model\Source\Role\Permission;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\Ca\Model\Source\Role\Permission
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Permission type list
     */
    const ALLOW = 'allow';
    const DENY = 'deny';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ALLOW,
                'label' => __('Allow')
            ],
            [
                'value' => self::DENY,
                'label' => __('Deny')
            ]
        ];
    }
}
