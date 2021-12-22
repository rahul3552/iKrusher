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

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GroupId
 * @package Aheadworks\Ca\Model\Customer\Builder
 */
class GroupId
{
    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->companyRepository = $companyRepository;
    }

    /**
     * Set group id to customer
     *
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws NoSuchEntityException
     */
    public function set($customer)
    {
        $companyId = $customer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
        $company = $this->companyRepository->get($companyId);
        $customer->setGroupId($company->getCustomerGroupId());
        return $customer;
    }
}
