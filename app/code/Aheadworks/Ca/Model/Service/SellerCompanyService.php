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
namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Model\Company\CompanyManagement;
use Aheadworks\Ca\Model\Source\Company\Status;
use Aheadworks\Ca\Model\Config;
use Exception;

/**
 * Class SellerCompanyService
 * @package Aheadworks\Ca\Model\Service
 */
class SellerCompanyService implements SellerCompanyManagementInterface
{
    /**
     * @var CompanyManagement
     */
    private $companyManagement;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CompanyManagement $companyManagement
     * @param CompanyRepositoryInterface $cartRepository
     * @param Config $config
     */
    public function __construct(
        CompanyManagement $companyManagement,
        CompanyRepositoryInterface $cartRepository,
        Config $config
    ) {
        $this->companyManagement = $companyManagement;
        $this->companyRepository = $cartRepository;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function createCompany($company, $customer)
    {
        if (!$company->getStatus()) {
            $company->setStatus(Status::PENDING_APPROVAL);
        }

        if (!$company->getSalesRepresentativeId()) {
            $salesRepresentativeId = $this->getDefaultSalesRepresentative($customer);
            $company->setSalesRepresentativeId($salesRepresentativeId);
        }
        return $this->companyManagement->createCompany($company, $customer);
    }

    /**
     * Get default sales representative for customer
     *
     * @param $customer
     * @return int|null
     */
    public function getDefaultSalesRepresentative($customer)
    {
        return $this->config->getDefaultSalesRepresentative($customer->getWebsiteId());
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function updateCompany($company, $customer)
    {
        return $this->companyManagement->updateCompany($company, $customer);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isBlockedCompany($companyId)
    {
        return $this->companyManagement->isBlocked($companyId);
    }

    /**
     * @inheritdoc
     */
    public function changeStatus($companyId, $status)
    {
        return $this->companyManagement->changeStatus($companyId, $status);
    }

    /**
     * @inheritdoc
     */
    public function getCompanyByCustomerId($customerId)
    {
        return $this->companyManagement->getCompanyByCustomerId($customerId);
    }
}
