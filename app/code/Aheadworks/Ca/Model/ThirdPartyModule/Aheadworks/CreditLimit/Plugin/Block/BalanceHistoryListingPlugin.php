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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;

/**
 * Class BalanceHistoryListingPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block
 */
class BalanceHistoryListingPlugin
{
    /**
     * @var CreditLimitManagement
     */
    private $creditLimitManagement;

    /**
     * @param CreditLimitManagement $creditLimitManagement
     */
    public function __construct(
        CreditLimitManagement $creditLimitManagement
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
    }

    /**
     * Apply additional params credit balance listing
     *
     * @param \Aheadworks\CreditLimit\Block\Customer\BalanceHistory\Listing $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetComponentParams($subject, $result)
    {
        if (!isset($result[CompanyUserInterface::CUSTOMER_ID])) {
            return $result;
        }

        $rootCustomer = $this->creditLimitManagement->getRootUserByCustomerId(
            $result[CompanyUserInterface::CUSTOMER_ID]
        );
        if ($rootCustomer) {
            $companyId = $rootCustomer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
            unset($result[CompanyUserInterface::CUSTOMER_ID]);
            $result[CompanyUserInterface::COMPANY_ID] = $companyId;
        }

        return $result;
    }

    /**
     * Check whether balance history grid is visible
     *
     * @param \Aheadworks\CreditLimit\Block\Customer\BalanceHistory\Listing $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        if (!$this->creditLimitManagement->isAvailableTransactions()) {
            $resultHtml = '';
        }

        return $resultHtml;
    }
}
