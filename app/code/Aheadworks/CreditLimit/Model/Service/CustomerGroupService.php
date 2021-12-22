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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Model\ResourceModel\CustomerGroupConfig;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerGroupConfigService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CustomerGroupService
{
    /**
     * @var CustomerGroupConfig
     */
    private $customerGroupConfigResource;

    /**
     * @param CustomerGroupConfig $customerGroupConfigResource
     */
    public function __construct(
        CustomerGroupConfig $customerGroupConfigResource
    ) {
        $this->customerGroupConfigResource = $customerGroupConfigResource;
    }

    /**
     * Get credit limit for customer group
     *
     * @param int $groupId
     * @param int $websiteId
     * @return float|null
     * @throws LocalizedException
     */
    public function getCreditLimit($groupId, $websiteId)
    {
        $creditLimitDefaultValue = null;
        $customerGroupsConfig = $this->customerGroupConfigResource->loadConfigValue($websiteId);
        foreach ($customerGroupsConfig as $customerGroupConfig) {
            if ($customerGroupConfig['customer_group_id'] == $groupId) {
                $creditLimit = $customerGroupConfig[SummaryInterface::CREDIT_LIMIT];
                $creditLimitDefaultValue = abs($creditLimit);
                break;
            }
        }

        return $creditLimitDefaultValue;
    }

    /**
     * Get credit limit values for website
     *
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function getCreditLimitValuesForWebsite($websiteId)
    {
        $data = $this->customerGroupConfigResource->loadData($websiteId);
        $creditLimitValues = [];
        foreach ($data as $dataRow) {
            $creditLimitValues[$dataRow['customer_group_id']] = abs($dataRow[SummaryInterface::CREDIT_LIMIT]);
        }

        return $creditLimitValues;
    }
}
