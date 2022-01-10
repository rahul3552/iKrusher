<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Model\Config\Source;

/**
 * Class SendTime
 * @package Mageplaza\CustomForm\Model\Config\Source
 */
class SendTime extends AbstractSource
{
    const IMMEDIATELY = 'immediately';
    const DAILY = 'daily';

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::DAILY => __('Daily'),
            self::IMMEDIATELY => __('Immediately'),
        ];
    }
}
