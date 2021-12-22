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
 * Class CrossMerger
 * @package Aheadworks\OneStepCheckout\Model\Layout
 */
class CrossMerger
{
    /**
     * @var RecursiveMerger
     */
    private $recursiveMerger;

    /**
     * @param RecursiveMerger $recursiveMerger
     */
    public function __construct(RecursiveMerger $recursiveMerger)
    {
        $this->recursiveMerger = $recursiveMerger;
    }

    /**
     * Fetch components definitions and merge into config
     *
     * @param array $config
     * @param array $sourceConfig
     * @return array
     */
    public function merge(array $config, array $sourceConfig)
    {
        foreach ($config as $code => $configData) {
            $config[$code] = isset($sourceConfig[$code])
                ? $this->recursiveMerger->merge($sourceConfig[$code], $configData)
                : [];
            if (empty($config[$code])) {
                unset($config[$code]);
            }
        }
        return $config;
    }
}
