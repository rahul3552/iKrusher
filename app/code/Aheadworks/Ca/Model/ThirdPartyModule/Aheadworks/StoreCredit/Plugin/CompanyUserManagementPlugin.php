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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\TransactionManagement;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CompanyUserManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin
 */
class CompanyUserManagementPlugin
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Manager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Move all customer balance to company balance
     *
     * @param \Aheadworks\Ca\Api\CompanyUserManagementInterface $subject
     * @param bool $result
     * @param int $userId
     * @return bool
     * @throws LocalizedException
     */
    public function afterAssignUserToCompany(
        $subject,
        $result,
        $userId
    ) {
        if ($result && $this->moduleManager->isAwStoreCreditModuleEnabled()) {
            $this->getTransactionManagement()->moveCustomerBalanceToCompanyBalance($userId);
        }
        return $result;
    }

    /**
     * Get transaction management
     *
     * @return TransactionManagement
     */
    public function getTransactionManagement()
    {
        return $this->objectManager->get(TransactionManagement::class);
    }
}
