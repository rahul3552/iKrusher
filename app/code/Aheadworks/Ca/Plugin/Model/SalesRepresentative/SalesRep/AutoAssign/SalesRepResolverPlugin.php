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
namespace Aheadworks\Ca\Plugin\Model\SalesRepresentative\SalesRep\AutoAssign;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\SalesRepresentative\Model\SalesRep\AutoAssign\SalesRepResolver;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class SalesRepResolverPlugin
 * @package Aheadworks\Ca\Plugin\Model\SalesRepresentative\SalesRep\AutoAssign
 */
class SalesRepResolverPlugin
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyRepositoryInterface $companyRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CompanyRepositoryInterface $companyRepository,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->companyRepository = $companyRepository;
        $this->logger = $logger;
    }

    /**
     * Set company sales representative
     *
     * @param SalesRepResolver $salesRepResolver
     * @param int|null $result
     * @param CustomerInterface|int $customer
     * @return int|null
     */
    public function afterGetSalesRepId(
        SalesRepResolver $salesRepResolver,
        $result,
        $customer
    ) {
        $customer = is_numeric($customer)
            ? $this->getCustomer($customer)
            : $customer;
        /** @var CompanyUserInterface $companyUser */
        $companyUser = $customer ? $customer->getExtensionAttributes()->getAwCaCompanyUser() : null;
        if ($companyUser) {
            $company = $this->getCompany($companyUser->getCompanyId());
            $result = $company ? $company->getSalesRepresentativeId() : $result;
        }
        return $result;
    }

    /**
     * Get customer
     *
     * @param int $customerId
     * @return CustomerInterface|null
     */
    private function getCustomer($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            $customer = null;
        } catch (LocalizedException $e) {
            $this->logger->error($e);
            $customer = null;
        }
        return $customer;
    }

    /**
     * Get company
     *
     * @param int $companyId
     * @return CompanyInterface|null
     */
    private function getCompany($companyId)
    {
        try {
            $company = $this->companyRepository->get($companyId);
        } catch (NoSuchEntityException $e) {
            $company = null;
        }
        return $company;
    }
}
