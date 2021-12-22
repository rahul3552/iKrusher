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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\DB\Select;

/**
 * Class QuoteListPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class QuoteListPlugin
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var \Aheadworks\Ctq\Model\ResourceModel\Quote\Collection|null
     */
    private $adjustedQuoteList;

    /**
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param AuthorizationManagementInterface $authorizationManagement
     */
    public function __construct(
        CompanyUserManagementInterface $companyUserManagement,
        AuthorizationManagementInterface $authorizationManagement
    ) {
        $this->companyUserManagement = $companyUserManagement;
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * Extend quote collection by filtering for all company users
     *
     * @param \Aheadworks\Ctq\ViewModel\Customer\QuoteList $subject
     * @param \Aheadworks\Ctq\Model\ResourceModel\Quote\Collection $quoteCollection
     * @return mixed
     */
    public function afterGetQuoteList($subject, $quoteCollection)
    {
        if (null === $this->adjustedQuoteList) {
            $this->adjustedQuoteList = $quoteCollection;
            if ($this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_view')) {
                if ($currentUser = $this->companyUserManagement->getCurrentUser()) {
                    /** @var CompanyUserInterface $companyUser */
                    $companyUser = $currentUser->getExtensionAttributes()->getAwCaCompanyUser();
                    $customers = $this->companyUserManagement->getChildUsersIds($companyUser->getCustomerId());
                    $this->adjustedQuoteList->getSelect()->reset(Select::WHERE);
                    $this->adjustedQuoteList
                        ->addFieldToFilter(
                            \Aheadworks\Ctq\Api\Data\QuoteInterface::CUSTOMER_ID,
                            ['in' => $customers]
                        );
                }
            }
        }

        return $this->adjustedQuoteList;
    }
}
