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
 * Class SelectiveMerger
 * @package Aheadworks\OneStepCheckout\Model\Layout
 */
class SelectiveMerger
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
     * @param array $toMerge
     * @return array
     */
    public function merge(array $config, array $sourceConfig, array $toMerge)
    {
        foreach ($sourceConfig as $code => $sourceConfigData) {
            if (in_array($code, $toMerge)) {
                if (!isset($config[$code])) {
                    $config[$code] = [];
                }
                $config[$code] = $this->recursiveMerger->merge($config[$code], $sourceConfigData);
            }
        }
        return $config;
    }
}
