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
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Aggregation\PeriodProvider;

use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Aggregation\PeriodProviderInterface;

/**
 * Class Day
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Aggregation\PeriodProvider
 */
class Day implements PeriodProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPeriods($fromDate, $toDate)
    {
        $periods = [];
        $date = (new \DateTime($fromDate))->setTime(0, 0, 0);
        $endDate = (new \DateTime($toDate))->setTime(0, 0, 0);

        while ($date <= $endDate) {
            $periodDate = $date->format('Y-m-d');
            $periods[] = ['from' => $periodDate, 'to' => $periodDate];
            $date->modify('+1 day');
        }

        return $periods;
    }
}
