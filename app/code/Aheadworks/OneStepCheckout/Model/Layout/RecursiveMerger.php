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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Layout;

/**
 * Class RecursiveMerger
 * @package Aheadworks\OneStepCheckout\Model\Layout
 */
class RecursiveMerger
{
    /**
     * Perform recursive config merging
     *
     * @param array $target
     * @param array $source
     * @return array
     */
    public function merge(array $target, array $source)
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                if (!isset($target[$key])) {
                    $target[$key] = [];
                }
                $target[$key] = $this->merge($target[$key], $value);
            } else {
                $target[$key] = $value;
            }
        }
        return $target;
    }
}
