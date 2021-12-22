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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Backend\Model\UrlInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;

/**
 * Class CustomerCompanyMessage
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\ViewModel
 */
class CustomerCompanyMessage implements ArgumentInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param CompanyRepositoryInterface $companyRepository
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        CompanyRepositoryInterface $companyRepository,
        UrlInterface $backendUrl
    ) {
        $this->objectManager = $objectManager;
        $this->companyRepository = $companyRepository;
        $this->backendUrl = $backendUrl;
    }

    /**
     * Prepare company link
     *
     * @param int $companyId
     * @return string
     * @throws NoSuchEntityException
     */
    public function prepareCompanyLink($companyId)
    {
        $company = $this->companyRepository->get($companyId);
        $companyUrl = $this->backendUrl->getUrl(
            "aw_ca/company/edit",
            [
                CompanyInterface::ID => $company->getId()
            ]
        );

        return "<a href ='" . $companyUrl . "'>" . $company->getName() . "</a>";
    }

    /**
     * Prepare sales representative link
     *
     * @param int $companyId
     * @return string
     */
    public function prepareSalesRepLink($companyId)
    {
        try {
            $company = $this->companyRepository->get($companyId);
            $userProfileRepository = $this->objectManager->get(
                \Aheadworks\Bup\Api\UserProfileMetadataRepositoryInterface::class
            );
            $userProfile = $userProfileRepository->get($company->getSalesRepresentativeId());
            $adminUserUrl = $this->backendUrl->getUrl(
                "admin/user/edit",
                [
                    'user_id' => $userProfile->getUserId()
                ]
            );
            $salesRepLink = "<a href ='" . $adminUserUrl . "'>" . $userProfile->getDisplayName() . "</a>";
        } catch (NoSuchEntityException $exception) {
            $salesRepLink = __('not selected');
        }

        return $salesRepLink;
    }
}
