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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\Plugin\Ui;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\Ui\CompanyNotificationApplier;

/**
 * Class SalesRepFieldsetPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\SalesRepresentative\Plugin\Ui
 */
class SalesRepFieldsetPlugin
{
    /**
     * @var CompanyNotificationApplier
     */
    private $notificationApplier;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param CompanyNotificationApplier $notificationApplier
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        CompanyNotificationApplier $notificationApplier,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->notificationApplier = $notificationApplier;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Prepare sales representative fieldset for message
     *
     * @param \Aheadworks\SalesRepresentative\Ui\Component\Form\Customer\SalesRepFieldset $subject
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforePrepare($subject)
    {
        $customerId = $subject->getContext()->getRequestParam('id');
        if ($customerId) {
            $rootCustomer = $this->companyUserManagement->getRootUserForCustomer($customerId);
            if ($rootCustomer) {
                $this->notificationApplier->hideSalesRepComponents($subject);
                $companyId = $rootCustomer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
                $this->notificationApplier->applyNotification($subject, $companyId);
            }
        }

        return null;
    }
}
