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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\GroupManagement;

/**
 * Class Config
 *
 * @package Aheadworks\QuickOrder\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_ENABLED = 'aw_quick_order/general/enabled';
    const XML_PATH_GENERAL_CUSTOMER_GROUP_LIST_TO_BE_ENABLED
        = 'aw_quick_order/general/customer_group_list_to_be_enabled';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnabled($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if enabled for customer group
     *
     * @param int $customerGroupId
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnabledForCustomerGroup($customerGroupId, $websiteId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_CUSTOMER_GROUP_LIST_TO_BE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        $customerGroups = explode(',', $value);
        return $value !== null
            && (in_array(GroupManagement::CUST_GROUP_ALL, $customerGroups)
            || in_array($customerGroupId, $customerGroups));
    }
}
