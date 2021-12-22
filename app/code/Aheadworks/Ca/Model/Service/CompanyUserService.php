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

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\GroupRepositoryInterface;
use Aheadworks\Ca\Model\Customer\Checker\ConvertToCompanyAdmin\Checker as ConvertToCompanyAdminChecker;
use Aheadworks\Ca\Model\Customer\CompanyUser\Notifier;
use Aheadworks\Ca\Model\Customer\Checker\EmailAvailability\Checker as EmailAvailabilityChecker;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Ca\Model\Customer\CompanyUser\ExtensionAttributesBuilder;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class CompanyUserService
 * @package Aheadworks\Ca\Model\Service
 */
class CompanyUserService implements CompanyUserManagementInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var EmailAvailabilityChecker
     */
    private $emailAvailabilityChecker;

    /**
     * @var ConvertToCompanyAdminChecker
     */
    private $convertToCompanyAdminChecker;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var ExtensionAttributesBuilder
     */
    private $companyUserExtAttributesBuilder;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param UserContextInterface $userContext
     * @param EmailAvailabilityChecker $emailAvailabilityChecker
     * @param ConvertToCompanyAdminChecker $convertToCompanyAdminChecker
     * @param Notifier $notifier
     * @param ExtensionAttributesBuilder $extensionAttributesManagement
     * @param HttpContext $httpContext
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupRepositoryInterface $groupRepository,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        UserContextInterface $userContext,
        EmailAvailabilityChecker $emailAvailabilityChecker,
        ConvertToCompanyAdminChecker $convertToCompanyAdminChecker,
        Notifier $notifier,
        ExtensionAttributesBuilder $extensionAttributesManagement,
        HttpContext $httpContext
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->groupRepository = $groupRepository;
        $this->customerRepository = $customerRepository;
        $this->accountManagement = $accountManagement;
        $this->userContext = $userContext;
        $this->emailAvailabilityChecker = $emailAvailabilityChecker;
        $this->convertToCompanyAdminChecker = $convertToCompanyAdminChecker;
        $this->notifier = $notifier;
        $this->companyUserExtAttributesBuilder = $extensionAttributesManagement;
        $this->httpContext = $httpContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUser()
    {
        $user = null;
        $userId = $this->userContext->getUserId();

        if (!$userId) {
            $companyInfo = $this->httpContext->getValue('company_info');
            $userId = isset($companyInfo) ? $companyInfo[CompanyUserInterface::CUSTOMER_ID] : null;
        }

        try {
            $customer = $this->customerRepository->getById($userId);
            if ($customer->getExtensionAttributes()
                && $customer->getExtensionAttributes()->getAwCaCompanyUser()
            ) {
                $user = $customer;
            }
        } catch (\Exception $e) {
            $user = null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootUserForCustomer($customerId)
    {
        $rootCustomer = null;
        try {
            $customer = $this->customerRepository->getById($customerId);
            if ($companyUser = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $rootCustomer = $this->getRootUserForCompany($companyUser->getCompanyId());
            }
        } catch (\Exception $e) {
            $rootCustomer = null;
        }
        return $rootCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootUserForCompany($companyId)
    {
        $rootUser = null;
        $this->searchCriteriaBuilder
            ->addFilter('aw_ca_customer_by_company_id', $companyId)
            ->addFilter('aw_ca_customer_is_root', null);

        $result = $this->customerRepository->getList($this->searchCriteriaBuilder->create());

        $items = $result->getItems();
        if ($result->getItems()) {
            $rootUser = reset($items);
        }

        return $rootUser;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllUserForCompany($companyId)
    {
        $this->searchCriteriaBuilder
            ->addFilter('aw_ca_customer_by_company_id', $companyId);

        $result = $this->customerRepository->getList($this->searchCriteriaBuilder->create());

        return $result->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllUsersIdsForCompany($companyId)
    {
        $customerIds = [];
        foreach ($this->getAllUserForCompany($companyId) as $customer) {
            $customerIds[] = $customer->getId();
        }
        return $customerIds;
    }

    /**
     * {@inheritdoc}
     */
    public function saveUser($user)
    {
        if ($user->getId()) {
            return $this->customerRepository->save($user);
        } else {
            $user = $this->accountManagement->createAccount($user);
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $user->getExtensionAttributes()->getAwCaCompanyUser();
            if (!$companyUser->getIsRoot()) {
                $this->notifier->notifyOnNewUserCreated($user);
            }
            return $user;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildUsers($userId)
    {
        try {
            $customers = [];
            $customer = $this->customerRepository->getById($userId);
            /** @var CompanyUserInterface $customerCompany */
            if ($customerCompany = $customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $group = $this->groupRepository->get($customerCompany->getCompanyGroupId());

                $this->searchCriteriaBuilder
                    ->addFilter('aw_ca_customer_by_group_path', $group->getPath());

                $customers = $this->customerRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            }
        } catch (\Exception $e) {
            $customers = [];
        }

        return $customers;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildUsersIds($userId)
    {
        $customerIds = [];
        foreach ($this->getChildUsers($userId) as $customer) {
            $customerIds[] = $customer->getId();
        }

        return $customerIds;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmailAvailable($email, $websiteId = null)
    {
        return $this->emailAvailabilityChecker->check($email, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableConvertToCompanyAdmin($email, $websiteId = null)
    {
        return $this->convertToCompanyAdminChecker->check($email, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function assignUserToCompany($userId, $companyId)
    {
        $customer = $this->customerRepository->getById($userId);
        if ($customer->getExtensionAttributes()
            && $customer->getExtensionAttributes()->getAwCaCompanyUser()
            && $customer->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot()
        ) {
            return false;
        }

        $this->companyUserExtAttributesBuilder->setAwCompanyUserIfNotIsset($customer);
        $rootCompanyUser = $this->getRootUserForCompany($companyId);
        if ($customer->getWebsiteId() != $rootCompanyUser->getWebsiteId()) {
            return false;
        }

        $currentCompany = $rootCompanyUser->getExtensionAttributes()->getAwCaCompanyUser();
        $customer->getExtensionAttributes()->getAwCaCompanyUser()
            ->setCompanyGroupId($currentCompany->getCompanyGroupId())
            ->setCompanyId($companyId);

        $this->companyUserExtAttributesBuilder->setAdditionalAttributes($customer);
        $this->saveUser($customer);

        return true;
    }
}
