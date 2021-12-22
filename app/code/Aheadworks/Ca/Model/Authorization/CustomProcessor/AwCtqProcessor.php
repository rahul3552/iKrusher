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
namespace Aheadworks\Ca\Model\Authorization\CustomProcessor;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AwCtqProcessor
 *
 * @package Aheadworks\Ca\Model\Authorization\CustomProcessor
 */
class AwCtqProcessor implements ProcessorInterface
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
     * @inheritdoc
     */
    public function isAllowed($resource)
    {
        $result = true;
        $module = explode('::', $resource)[0];
        if ($module != Manager::AW_CTQ_MODULE_NAME) {
            return $result;
        }

        if ($currentUser = $this->companyUserManagement->getCurrentUser()) {
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $currentUser->getExtensionAttributes()->getAwCaCompanyUser();
            try {
                $company = $this->companyRepository->get($companyUser->getCompanyId());
                $result = $company->getIsAllowedToQuote();
            } catch (NoSuchEntityException $exception) {
                $result = false;
            }
        }

        return $result;
    }
}
