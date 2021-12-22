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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PaymentManagement
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\PaymentRestrictions\Model
 */
class PaymentManagement
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->companyRepository = $companyRepository;
    }

    /**
     * retrieve allowed company payment methods
     *
     * @return array
     */
    public function getAllowedCompanyPaymentMethods()
    {
        $allowedPaymentMethods = [];
        if ($currentUser = $this->companyUserManagement->getCurrentUser()) {
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $currentUser->getExtensionAttributes()->getAwCaCompanyUser();
            try {
                $company = $this->companyRepository->get($companyUser->getCompanyId());
                $allowedPaymentMethods = $company->getAllowedPaymentMethods() ?? [];
            } catch (NoSuchEntityException $exception) {
                $allowedPaymentMethods = [];
            }
        }

        return $allowedPaymentMethods;
    }
}
