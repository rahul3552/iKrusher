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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Model\Config\Source\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ShowOn
 * @package Mageplaza\ProductAttachments\Model\Config\Source\System
 */
class ShowOn implements ArrayInterface
{
    const TAB = '3';
    const INLINE = '1';
    const SIDEBAR = '2';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            '' => __('-- Please select --'),
            self::TAB => __('Product Tab'),
            self::INLINE => __('Under "Add To Cart" button'),
            self::SIDEBAR => __('Sidebar')
        ];
    }
}
