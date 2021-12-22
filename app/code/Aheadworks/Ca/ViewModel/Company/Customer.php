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
namespace Aheadworks\Ca\ViewModel\Company;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class Customer
 *
 * @package Aheadworks\Ca\ViewModel\Company
 */
class Customer implements ArgumentInterface
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Get current company user
     *
     * @return CustomerInterface
     */
    public function getCurrentCompanyUser()
    {
        return $this->companyUserManagement->getCurrentUser();
    }

    /**
     * Get root company user
     *
     * @param int $companyId
     * @return CustomerInterface
     */
    public function getRootCompanyUser($companyId)
    {
        return $this->companyUserManagement->getRootUserForCompany($companyId);
    }
}
