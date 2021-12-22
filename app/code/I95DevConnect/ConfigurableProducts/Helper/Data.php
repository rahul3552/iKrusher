<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Helper;

/**
 * configurable product class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * scopeConfig for system Configuration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     *
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Check configurable product is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'configurableproducts/i95dev_enabled_settings/is_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}
