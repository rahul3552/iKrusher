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
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TypeNotice
 * @package Mageplaza\AgeVerification\Model\Config\Source
 */
class VerifyType implements ArrayInterface
{
    const YESNO = 1;
    const INPUT_DOB = 2;
    const CHECKBOX = 3;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::YESNO, 'label' => __('Yes/No Type')],
            ['value' => self::INPUT_DOB, 'label' => __('Input DOB Type')],
            ['value' => self::CHECKBOX, 'label' => __('Checkbox Type')],
        ];
    }
}
