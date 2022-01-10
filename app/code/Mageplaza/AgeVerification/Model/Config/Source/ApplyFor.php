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
 * Class ApplyFor
 * @package Mageplaza\AgeVerification\Model\Config\Source
 */
class ApplyFor implements ArrayInterface
{
    const CHECKOUT_CART_PAGE = 'checkout_cart_index';
    const CHECKOUT_PAGE = 'checkout_index_index';
    const CATALOG_SEARCH_PAGE = 'catalogsearch_result_index';

    /**
     * Options getter
     *
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
     * @return array
     */
    protected function toArray()
    {
        return [
            0 => __('-- Please Select --'),
            self::CHECKOUT_CART_PAGE => __('Shopping Cart Page'),
            self::CHECKOUT_PAGE => __('Checkout Page'),
            self::CATALOG_SEARCH_PAGE => __('Catalog Search Page'),
        ];
    }
}
