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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior;

/**
 * Class Formatter
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior
 */
class Formatter
{
    /**
     * Format value in percents
     *
     * @param float $value
     * @return string
     */
    public function formatPercents($value)
    {
        return number_format($value, 2) . '%';
    }
}
