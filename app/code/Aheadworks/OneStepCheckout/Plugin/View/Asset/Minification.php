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
namespace Aheadworks\OneStepCheckout\Plugin\View\Asset;

use Aheadworks\OneStepCheckout\Model\Config\Initial\Exploder;
use Magento\Framework\View\Asset\Minification as AssetMinification;

/**
 * Class Minification
 * @package Aheadworks\OneStepCheckout\Plugin\View\Asset
 */
class Minification
{
    /**
     * @var Exploder
     */
    private $configExploder;

    /**
     * @var array
     */
    private $cache;

    /**
     * @param Exploder $configExploder
     */
    public function __construct(Exploder $configExploder)
    {
        $this->configExploder = $configExploder;
    }

    /**
     * @param AssetMinification $subject
     * @param \Closure $proceed
     * @param string $contentType
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetExcludes(AssetMinification $subject, \Closure $proceed, $contentType)
    {
        if (!isset($this->cache[AssetMinification::XML_PATH_MINIFICATION_EXCLUDES][$contentType])) {
            $this->cache[AssetMinification::XML_PATH_MINIFICATION_EXCLUDES][$contentType] = [];
            $xmlPath = sprintf(AssetMinification::XML_PATH_MINIFICATION_EXCLUDES, $contentType);
            foreach ($this->configExploder->explodeByPath($xmlPath) as $rawValue) {
                $rawValues = !is_array($rawValue)
                    ? explode("\n", $rawValue)
                    : $rawValue;
                foreach ($rawValues as $value) {
                    if (trim($value) != '') {
                        $this->cache[AssetMinification::XML_PATH_MINIFICATION_EXCLUDES][$contentType][] = trim($value);
                    }
                }
            }
        }
        return $this->cache[AssetMinification::XML_PATH_MINIFICATION_EXCLUDES][$contentType];
    }
}
