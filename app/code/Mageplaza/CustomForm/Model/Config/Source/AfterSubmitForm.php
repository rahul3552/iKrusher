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
 * Class AfterSubmitForm
 * @package Mageplaza\CustomForm\Model\Config\Source
 */
class AfterSubmitForm extends AbstractSource
{
    const REDIRECT_URL = 'url';
    const KEEP_CURRENT = 'current';
    const REDIRECT_CMS = 'cms';

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::KEEP_CURRENT => __('Keep current page'),
            self::REDIRECT_URL => __('Redirect to URL'),
            self::REDIRECT_CMS => __('Redirect to CMS page'),
        ];
    }
}
