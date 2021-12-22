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
namespace Aheadworks\QuickOrder\Model\Service;

use Aheadworks\QuickOrder\Api\CustomerManagementInterface;
use Aheadworks\QuickOrder\Model\Config;

/**
 * Class CustomerService
 *
 * @package Aheadworks\QuickOrder\Model\Service
 */
class CustomerService implements CustomerManagementInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function isActiveForCustomerGroup($customerGroupId, $websiteId = null)
    {
        return $this->config->isEnabled($websiteId)
            && $this->config->isEnabledForCustomerGroup($customerGroupId, $websiteId);
    }
}
