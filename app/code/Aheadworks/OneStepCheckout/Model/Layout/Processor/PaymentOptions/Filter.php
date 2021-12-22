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
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\PaymentOptions;

use Aheadworks\OneStepCheckout\Model\Config;

/**
 * Class Filter
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\PaymentOptions
 */
class Filter
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
     * Filter payment options definitions
     *
     * @param array $config
     * @return array
     */
    public function filter(array $config)
    {
        if (isset($config['discount']) && !$this->config->isApplyDiscountCodeEnabled()) {
            unset($config['discount']);
        }
        return $config;
    }
}
