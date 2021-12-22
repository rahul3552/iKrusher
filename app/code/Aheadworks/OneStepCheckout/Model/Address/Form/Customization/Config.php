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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\Customization;

use Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Config\Reader;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data as ConfigData;

/**
 * Class Config
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\Customization
 */
class Config extends ConfigData
{
    /**
     * @param Reader $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        $cacheId = 'osc_attribute_customization'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}
