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
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Totals;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Sorter
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\Totals
 */
class Sorter
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Sort totals
     *
     * @param array $config
     * @return array
     */
    public function sort(array $config)
    {
        $sortData = $this->scopeConfig->getValue('sales/totals_sort');
        foreach ($config as $code => &$configData) {
            $sortTotalCode = str_replace('-', '_', $code);
            if (isset($sortData[$sortTotalCode]) && isset($config[$code])) {
                $configData['sortOrder'] = $sortData[$sortTotalCode];
            }
        }
        return $config;
    }
}
