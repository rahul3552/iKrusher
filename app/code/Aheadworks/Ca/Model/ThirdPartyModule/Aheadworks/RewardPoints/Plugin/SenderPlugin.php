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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Model\SummaryManagement;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SenderPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RewardPoints\Plugin
 */
class SenderPlugin
{
    /**
     * @var SummaryManagement
     */
    private $summaryManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param SummaryManagement $summaryManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        SummaryManagement $summaryManagement,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->summaryManagement = $summaryManagement;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Adjust balance variables for company user customer
     *
     * @param \Aheadworks\RewardPoints\Model\Sender $subject
     * @param CustomerInterface $customer
     * @param string $comment
     * @param int $points
     * @param int $pointsBalance
     * @param string $moneyBalance
     * @param string $expireDate
     * @param int $storeId
     * @param string $template
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeSendNotification(
        $subject,
        $customer,
        $comment,
        $points,
        $pointsBalance,
        $moneyBalance,
        $expireDate,
        $storeId,
        $template
    ) {
        $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customer->getId());
        if ($rootCustomer) {
            $pointsBalance = $this->summaryManagement->getCustomerPointsBalance($customer->getId());
            $moneyBalance = $this->summaryManagement->getCustomerPointsBalanceBaseCurrency($customer);
        }

        return [
            $customer,
            $comment,
            $points,
            $pointsBalance,
            $moneyBalance,
            $expireDate,
            $storeId,
            $template
        ];
    }
}
