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
namespace Aheadworks\Ca\Plugin\App\Action;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class ContextPlugin
 * @package Aheadworks\Ca\Plugin\App\Action
 */
class ContextPlugin
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var SellerCompanyManagementInterface
     */
    private $sellerCompanyManagement;

    /**
     * @param Session $customerSession
     * @param HttpContext $httpContext
     * @param SellerCompanyManagementInterface $sellerCompanyManagement
     */
    public function __construct(
        Session $customerSession,
        HttpContext $httpContext,
        SellerCompanyManagementInterface $sellerCompanyManagement
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->sellerCompanyManagement = $sellerCompanyManagement;
    }

    /**
     * Set company information to HTTP context
     *
     * @param ActionInterface $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(
        ActionInterface $subject
    ) {
        $customerId = $this->customerSession->getCustomerId();
        $company = $this->sellerCompanyManagement->getCompanyByCustomerId($customerId);

        if ($company) {
            $this->httpContext->setValue(
                'company_info', [
                    CompanyInterface::IS_ALLOWED_TO_QUOTE => $company->getIsAllowedToQuote(),
                    CompanyUserInterface::COMPANY_ID => $company->getId(),
                    CompanyUserInterface::CUSTOMER_ID => $customerId
                ], []
            );
        }
    }
}
