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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;

/**
 * Class BuyerPermissionManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class BuyerPermissionManagementPlugin
{
    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        AuthorizationManagementInterface $authorizationManagement,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->authorizationManagement = $authorizationManagement;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Check is customer allowed to quote
     *
     * @param BuyerPermissionManagementInterface $subject
     * @param callable $proceed
     * @param array $args
     * @return bool
     */
    public function aroundCanRequestQuote($subject, callable $proceed, ...$args)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser) {
            return $this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_allow_using');
        } else {
            return $proceed(...$args);
        }
    }

    /**
     * Check is customer allowed to quote list
     *
     * @param BuyerPermissionManagementInterface $subject
     * @param bool $result
     * @param int $customerGroup
     * @param int|null $storeId
     * @return bool
     */
    public function afterIsAllowQuoteList(
        $subject,
        $result,
        $customerGroup,
        $storeId = null
    ) {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        return $currentUser
            ? $result && $this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_allow_using')
            : $result;
    }
}
