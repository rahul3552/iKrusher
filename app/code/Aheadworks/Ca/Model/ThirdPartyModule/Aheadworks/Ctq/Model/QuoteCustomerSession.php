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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Model;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * Class QuoteCustomerSession
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Model
 */
class QuoteCustomerSession extends CustomerSession
{
    /**
     * @var array
     */
    private $customerIdsCache = [];

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        $customerId = parent::getCustomerId();
        $quoteId = $this->getQuoteId();

        if ($quoteId
            && $this->getAuthorizationManagement()->isAllowedByResource('Aheadworks_Ctq::company_quotes_view')
        ) {
            if (!isset($this->customerIdsCache[$customerId])) {
                $this->customerIdsCache[$customerId] = $this->getCompanyUserManagement()
                    ->getChildUsersIds($customerId);
            }

            $customerIds = $this->customerIdsCache[$customerId];
            $quoteCustomerId = $this->getQuote($quoteId)->getCustomerId();

            if (in_array($quoteCustomerId, $customerIds)) {
                $customerId = $quoteCustomerId;
            }
        }

        return $customerId;
    }

    /**
     * Retrieve authorization management
     *
     * @return AuthorizationManagementInterface
     */
    private function getAuthorizationManagement()
    {
        return ObjectManager::getInstance()->get(AuthorizationManagementInterface::class);
    }

    /**
     * Retrieve company user management
     *
     * @return CompanyUserManagementInterface
     */
    private function getCompanyUserManagement()
    {
        return ObjectManager::getInstance()->get(CompanyUserManagementInterface::class);
    }

    /**
     * Retrieve quote id
     *
     * @return int
     */
    private function getQuoteId()
    {
        $request = ObjectManager::getInstance()->get(RequestInterface::class);
        return $request->getParam('quote_id');
    }

    /**
     * Retrieve quote
     *
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     */
    private function getQuote($quoteId)
    {
        $quoteRepository = ObjectManager::getInstance()->get(QuoteRepositoryInterface::class);
        return $quoteRepository->get($quoteId);
    }
}
