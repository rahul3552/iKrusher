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
namespace Aheadworks\OneStepCheckout\Model\Report;

/**
 * Class Aggregation
 * @package Aheadworks\OneStepCheckout\Model\Report
 */
class Aggregation
{
    /**
     * Get aggregations
     *
     * @return array
     */
    public function getAggregations()
    {
        return ['day', 'week', 'month', 'quarter', 'year'];
    }
}
