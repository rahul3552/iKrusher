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
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices\GoogleAutocomplete\RegionMap;
use Aheadworks\OneStepCheckout\Model\Config;

/**
 * Interface GoogleAutocomplete
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices
 */
class GoogleAutocomplete implements ServiceComponentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var RegionMap
     */
    private $regionMap;

    /**
     * @param Config $config
     * @param RegionMap $regionMap
     */
    public function __construct(
        Config $config,
        RegionMap $regionMap
    ) {
        $this->config = $config;
        $this->regionMap = $regionMap;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->config->isGoogleAutoCompleteEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function configure(&$jsLayout)
    {
        if (!isset($jsLayout['config'])) {
            $jsLayout['config'] = [];
        }
        $jsLayout['config']['regionMap'] = $this->regionMap->getMap();
    }
}
