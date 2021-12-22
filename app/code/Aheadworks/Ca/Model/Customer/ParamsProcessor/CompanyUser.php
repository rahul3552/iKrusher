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
namespace Aheadworks\Ca\Model\Customer\ParamsProcessor;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class CompanyUser
 *
 * @package Aheadworks\Ca\Model\Customer\ParamsProcessor
 */
class CompanyUser
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
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Remove sales representative for company user
     *
     * @param array $originalData
     * @return array
     */
    public function process($originalData)
    {
        $data = $originalData;
        foreach ($data as &$item) {
            if (isset($item['aw_salesrep_id']) && isset($item['entity_id'])) {
                $rootUser = $this->companyUserManagement->getRootUserForCustomer($item['entity_id']);
                if ($rootUser) {
                    $companyId = $rootUser->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
                    $company = $this->companyRepository->get($companyId);
                    if ($item['aw_salesrep_id'] != $company->getSalesRepresentativeId()) {
                        $originalData = [];
                    }
                }
            }
        }
        return $originalData;
    }
}
