<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Helper;

use \Magento\Framework\App\Helper\Context;
use \Magento\Directory\Model\CurrencyFactory;
use \Magento\Store\Model\StoreManagerInterface;

/**
 * Helper Class for Module
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * scopeConfig for system Configuration
     *
     * @var string
     */
    public $scopeConfig;

    public $logger;

    /**
     * @var CurrencyFactory
     */
    public $priceCurrencyFactory;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param CurrencyFactory $priceCurrencyFactory
     * @param StoreManagerInterface $storeManager
     *
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CurrencyFactory $priceCurrencyFactory,
        StoreManagerInterface $storeManager
    ) {

        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrencyFactory = $priceCurrencyFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Check Price Level module enable/disable
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'i95dev_pricelevel/active_display/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Convert value from base currency to user currency
     * @param $value
     * @return float|int
     */
    public function convertPrice($value)
    {
        $currencyCodeTo = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $currencyCodeFrom = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        $rate = $this->priceCurrencyFactory->create()->load($currencyCodeFrom)
            ->getAnyRate($currencyCodeTo);
        return $value * $rate;
    }
}
