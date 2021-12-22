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
namespace Aheadworks\Ca\Model\Customer\Builder;

use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CompanyRoleId
 * @package Aheadworks\Ca\Model\Customer\Builder
 */
class CompanyRoleId
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository
    ) {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Set company role id to customer
     *
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws NoSuchEntityException
     */
    public function set($customer)
    {
        $companyId = $customer->getExtensionAttributes()
            ->getAwCaCompanyUser()->getCompanyId();
        $defaultUserRole = $this->roleRepository->getDefaultUserRole($companyId);
        $customer->getExtensionAttributes()->getAwCaCompanyUser()
            ->setCompanyRoleId($defaultUserRole->getId());
        return $customer;
    }
}
