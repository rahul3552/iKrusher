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
namespace Aheadworks\OneStepCheckout\Model\ConfigProvider;

use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\CarrierFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class DefaultShippingMethod
 * @package Aheadworks\OneStepCheckout\Model\ConfigProvider
 */
class DefaultShippingMethod
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CarrierFactory
     */
    private $carrierFactory;

    /**
     * @param Config $config
     * @param ScopeConfigInterface $scopeConfig
     * @param CarrierFactory $carrierFactory
     */
    public function __construct(
        Config $config,
        ScopeConfigInterface $scopeConfig,
        CarrierFactory $carrierFactory
    ) {
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * Get default shipping method config data
     *
     * @return array
     */
    public function getShippingMethod()
    {
        $configData = [];
        $method = $this->config->getDefaultShippingMethod();
        if ($method) {
            $methodComponents = explode('_', $method);
            $carrierCode = array_shift($methodComponents);
            $methodCode = implode('_', $methodComponents);

            $configData[ShippingMethodInterface::KEY_CARRIER_CODE] = $carrierCode;
            $configData[ShippingMethodInterface::KEY_METHOD_CODE] = $methodCode;

            $carriersConfig = $this->scopeConfig->getValue('carriers');
            foreach (array_keys($carriersConfig) as $carrCode) {
                if ($carrierCode == $carrCode) {
                    /** @var CarrierInterface $carrier */
                    $carrier = $this->carrierFactory->create($carrCode);
                    $methods = $carrier->getAllowedMethods();
                    foreach ($methods as $code => $title) {
                        if ($methodCode == $code) {
                            $configData[ShippingMethodInterface::KEY_METHOD_TITLE] = $title;
                            $configData[ShippingMethodInterface::KEY_CARRIER_TITLE] = $this->scopeConfig->getValue(
                                'carriers/' . $carrierCode . '/title',
                                ScopeInterface::SCOPE_STORE
                            );
                        }
                    }
                }
            }
        }
        return $configData;
    }
}
