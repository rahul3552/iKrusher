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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;

/**
 * Class TransactionServicePlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin
 */
class TransactionServicePlugin
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
     * Change transaction params before proceed
     *
     * @param \Aheadworks\CreditLimit\Api\TransactionManagementInterface $subject
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface $params
     * @return array
     */
    public function beforeCreateTransaction($subject, $params)
    {
        $rootCustomer = $this->creditLimitManagement->getRootUserByCustomerId($params->getCustomerId());
        if ($rootCustomer) {
            $companyId = $rootCustomer->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
            $params->setCustomerId($rootCustomer->getId());
            $params->setCompanyId($companyId);
        }

        return [$params];
    }
}
