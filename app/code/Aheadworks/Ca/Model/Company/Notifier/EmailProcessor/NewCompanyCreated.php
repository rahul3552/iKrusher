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
namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ca\Model\Magento\ModuleUser\UserRepository;

/**
 * Class PendingApproval
 *
 * @package Aheadworks\Ca\Model\Company\Notifier\EmailProcessor
 */
class NewCompanyCreated extends AbstractProcessor implements EmailProcessorInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param Config $config
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorInterface $variableProcessorComposite
     * @param UserRepository $userRepository
     */
    public function __construct(
        Config $config,
        CompanyUserManagementInterface $companyUserManagement,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorInterface $variableProcessorComposite,
        UserRepository $userRepository
    ) {
        parent::__construct(
            $config,
            $companyUserManagement,
            $storeManager,
            $emailMetadataFactory,
            $variableProcessorComposite
        );
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getNewCompanySubmittedTemplate($storeId);
    }

    /**
     * Retrieve recipient name
     *
     * @param CompanyInterface $company
     * @return string
     */
    protected function getRecipientName($company)
    {
        try {
            $user = $this->userRepository->getById($company->getSalesRepresentativeId());
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = '';
        }
        return $name;
    }

    /**
     * Retrieve recipient email
     *
     * @param CompanyInterface $company
     * @return string
     */
    protected function getRecipientEmail($company)
    {
        try {
            $user = $this->userRepository->getById($company->getSalesRepresentativeId());
            $email = $user->getEmail();
        } catch (\Exception $e) {
            $email = '';
        }
        return $email;
    }
}
