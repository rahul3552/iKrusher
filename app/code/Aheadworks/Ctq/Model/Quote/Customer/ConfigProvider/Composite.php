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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class Composite
 * @package Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider
 */
class Composite implements ConfigProviderInterface
{
    /**
     * @var ConfigProviderInterface[]
     */
    private $configProviders;

    /**
     * @param ConfigProviderInterface[] $configProviders
     */
    public function __construct(
        array $configProviders
    ) {
        $this->configProviders = $configProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->configProviders as $configProvider) {
            $config = array_merge_recursive($config, $configProvider->getConfig());
        }
        return $config;
    }
}
