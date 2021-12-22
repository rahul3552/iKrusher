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
namespace Aheadworks\OneStepCheckout\Model\DeliveryDate;

use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Config\Source\DeliveryDate\DisplayOption;

/**
 * Class ConfigProvider
 * @package Aheadworks\OneStepCheckout\Model\DeliveryDate
 */
class ConfigProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get delivery date options config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'isEnabled' => $this->config->getDeliveryDateDisplayOption() != DisplayOption::NO,
            'dateRestrictions' => [
                'weekdays' => $this->config->getDeliveryDateAvailableWeekdays(),
                'nonDeliveryPeriods' => $this->config->getNonDeliveryPeriods(), // todo: to current timezone
                'minOrderDeliveryPeriod' => $this->config->getMinOrderDeliveryPeriod()
            ]
        ];
    }
}
