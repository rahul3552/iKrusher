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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Region as RegionSource;

/**
 * Class RegionId
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute
 */
class RegionId implements ModifierInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var RegionSource
     */
    private $regionSource;

    /**
     * @param Config $config
     * @param RegionSource $regionSource
     */
    public function __construct(
        Config $config,
        RegionSource $regionSource
    ) {
        $this->config = $config;
        $this->regionSource = $regionSource;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($metadata, $addressType)
    {
        $defaultRegionId = $this->config->getDefaultRegionId();
        if ($defaultRegionId && is_numeric($defaultRegionId)) {
            $metadata['default'] = $defaultRegionId;
        }
        $metadata['options'] = $this->regionSource->getAllOptions();
        return $metadata;
    }
}
