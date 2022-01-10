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
 * Class FormStyle
 * @package Mageplaza\CustomForm\Model\Config\Source
 */
class FormStyle extends AbstractSource
{
    const STATIC_STYLE = 'static';
    const POPUP_STYLE = 'popup';

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::STATIC_STYLE => __('Static'),
            self::POPUP_STYLE => __('Popup after click button'),
        ];
    }
}
